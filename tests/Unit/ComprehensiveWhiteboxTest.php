<?php

namespace Tests\Unit;

use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\SecureHeaders;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamSetting;
use App\Models\ProctoringLog;
use App\Models\Question;
use App\Models\Role;
use App\Models\User;
use App\Policies\ExamAttemptPolicy;
use App\Policies\ExamPolicy;
use App\Services\ExamService;
use App\Services\ProctoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Tests\Support\CreatesZafProctorData;
use Tests\TestCase;

class ComprehensiveWhiteboxTest extends TestCase
{
    use CreatesZafProctorData;
    use RefreshDatabase;

    /** @test */
    public function user_role_scopes_and_approval_helpers_are_consistent(): void
    {
        $admin = $this->admin();
        $teacher = $this->teacher(['is_approved' => false]);
        $inactiveStudent = $this->student(['is_active' => false]);

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($teacher->isTeacher());
        $this->assertTrue($inactiveStudent->isStudent());
        $this->assertTrue($teacher->hasAnyRole([Role::ADMIN, Role::TEACHER]));
        $this->assertSame(1, User::byRole(Role::TEACHER)->pendingApproval()->count());
        $this->assertSame(1, User::byRole(Role::STUDENT)->where('is_active', false)->count());

        Auth::login($admin);
        $teacher->approve();

        $this->assertTrue($teacher->fresh()->isApproved());
        $this->assertSame($admin->id, $teacher->fresh()->approved_by);
        $this->assertNotNull($teacher->fresh()->approved_at);
    }

    /** @test */
    public function exam_status_time_windows_and_attempt_time_behaviour_are_calculated(): void
    {
        $teacher = $this->teacher();
        $course = $this->course($teacher);
        $flexible = $this->exam($teacher, $course);
        $scheduled = $this->scheduledExam($teacher, $course);
        $future = $this->scheduledExam($teacher, $course, [
            'start_time' => now()->addHour(),
            'end_time' => now()->addHours(2),
        ]);

        $this->assertTrue($flexible->isActive());
        $this->assertTrue($scheduled->isActive());
        $this->assertFalse($future->isActive());
        $this->assertTrue($flexible->hasStarted());
        $this->assertFalse($flexible->hasEnded());

        $student = $this->student();
        $attempt = $this->attempt($flexible, $student, [
            'started_at' => now()->subMinutes(30),
        ]);

        $this->assertTrue($attempt->isInProgress());
        $this->assertGreaterThan(0, $attempt->remaining_time);
        $this->assertFalse($attempt->hasTimeExpired());

        $expired = $this->attempt($flexible, $student, [
            'started_at' => now()->subMinutes(90),
        ]);

        $this->assertTrue($expired->hasTimeExpired());
        $this->assertSame(0, $expired->remaining_time);
    }

    /** @test */
    public function answer_and_attempt_scoring_cover_multiple_choice_and_essay_paths(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $exam = $this->exam($teacher, null, [], ['passing_score' => 70]);
        $mc = $this->multipleChoiceQuestion($exam, ['points' => 10]);
        $essay = $this->essayQuestion($exam, ['points' => 20]);
        $attempt = $this->attempt($exam, $student);

        $correctOption = $mc->options()->where('is_correct', true)->first();
        $mcAnswer = $this->answer($attempt, $mc, [
            'selected_option_id' => $correctOption->id,
        ]);
        $essayAnswer = $this->answer($attempt, $essay, [
            'essay_answer' => 'A detailed essay answer.',
        ]);

        $mcAnswer->grade();
        $essayAnswer->gradeEssay(14, 'Good enough');
        $attempt->calculateScore();

        $this->assertTrue($mcAnswer->fresh()->is_correct);
        $this->assertSame('10.00', $mcAnswer->fresh()->points_earned);
        $this->assertTrue($essayAnswer->fresh()->is_correct);
        $this->assertSame('14.00', $essayAnswer->fresh()->points_earned);
        $this->assertSame('24.00', $attempt->fresh()->score);
        $this->assertSame('80.00', $attempt->fresh()->percentage);
        $this->assertTrue($attempt->fresh()->is_passed);
    }

    /** @test */
    public function exam_service_enforces_payload_rules_and_deterministic_shuffle(): void
    {
        $service = app(ExamService::class);
        $teacher = $this->teacher();
        $student = $this->student();
        $course = $this->course($teacher);
        $this->enroll($student, $course);
        $exam = $this->exam($teacher, $course, [], [
            'shuffle_questions' => true,
            'shuffle_options' => true,
            'max_attempts' => 0,
        ]);
        $first = $this->multipleChoiceQuestion($exam);
        $second = $this->multipleChoiceQuestion($exam);
        $essay = $this->essayQuestion($exam);

        $attempt = $service->startExam($exam, $student->id, '127.0.0.1', 'Unit Agent');
        $orderOne = $service->getQuestionsForAttempt($exam, $attempt)->pluck('id')->all();
        $orderTwo = $service->getQuestionsForAttempt($exam, $attempt)->pluck('id')->all();

        $this->assertSame($orderOne, $orderTwo);
        $this->assertContains($first->id, $orderOne);
        $this->assertContains($second->id, $orderOne);

        $this->expectExceptionMessage('Payload jawaban tidak valid untuk soal esai.');
        $service->saveAnswer($attempt, $essay->id, $first->options()->first()->id);
    }

    /** @test */
    public function proctoring_service_resolves_thresholds_snapshots_summary_and_reviews(): void
    {
        Storage::fake('local');

        $teacher = $this->teacher();
        $student = $this->student();
        $exam = $this->exam($teacher, null, [], [
            'auto_submit_threshold' => 2,
            'max_tab_switches' => 9,
            'snapshot_interval' => 30,
        ]);
        $attempt = $this->attempt($exam, $student, [
            'submitted_at' => now(),
            'status' => ExamAttempt::STATUS_SUBMITTED,
        ]);
        $service = app(ProctoringService::class);

        $path = $service->storeSnapshotFromBase64($attempt, $this->tinyPngDataUri());
        $this->assertNotNull($path);
        Storage::disk('local')->assertExists($path);

        $logOne = $service->logViolation($attempt, ProctoringLog::TYPE_TAB_SWITCH, 'Tab moved');
        $logTwo = $service->logViolation($attempt, ProctoringLog::TYPE_FULLSCREEN_EXIT, 'Fullscreen exited');
        $snapshotLog = $this->proctoringLog($attempt, ['snapshot_path' => $path]);

        $summary = $service->getViolationSummary($attempt->fresh());
        $this->assertSame(3, $summary['total']);
        $this->assertSame(2, $attempt->fresh()->violation_count);
        $this->assertSame(1, $attempt->fresh()->tab_switch_count);
        $this->assertSame(1, $attempt->fresh()->fullscreen_exit_count);
        $this->assertTrue($attempt->fresh()->hasExceededViolations());
        $this->assertCount(1, $service->getSnapshots($attempt->fresh()));

        $reviewed = $service->markAsReviewed([$logOne->id, $logTwo->id], $teacher->id, 'Reviewed');
        $this->assertSame(2, $reviewed);
        $this->assertSame(2, ProctoringLog::where('is_reviewed', true)->count());
        $this->assertSame($snapshotLog->id, $attempt->fresh()->latestSnapshot->id);
    }

    /** @test */
    public function question_image_paths_are_normalized_without_losing_external_urls(): void
    {
        $question = new Question();

        $question->question_image = '/storage/questions/example.png';
        $this->assertSame('questions/example.png', $question->question_image);
        $this->assertSame('questions/example.png', $question->normalized_question_image_path);

        $question->question_image = 'public/questions/another.jpg';
        $this->assertSame('questions/another.jpg', $question->question_image);

        $external = 'https://cdn.example.test/assets/question.png';
        $question->question_image = $external;
        $this->assertSame($external, $question->question_image);
        $this->assertSame($external, $question->question_image_url);
    }

    /** @test */
    public function policies_cover_ownership_enrollment_attempt_interaction_and_review_access(): void
    {
        $owner = $this->teacher();
        $otherTeacher = $this->teacher();
        $student = $this->student();
        $admin = $this->admin();
        $course = $this->course($owner);
        $this->enroll($student, $course);
        $exam = $this->exam($owner, $course);
        $attempt = $this->attempt($exam, $student);

        $examPolicy = new ExamPolicy();
        $attemptPolicy = new ExamAttemptPolicy();

        $this->assertTrue($examPolicy->view($owner, $exam));
        $this->assertFalse($examPolicy->view($otherTeacher, $exam));
        $this->assertTrue($examPolicy->view($student, $exam));
        $this->assertTrue($examPolicy->start($student, $exam));
        $this->assertFalse($examPolicy->start($owner, $exam));
        $this->assertTrue($examPolicy->monitor($owner, $exam));
        $this->assertFalse($examPolicy->monitor($otherTeacher, $exam));

        $this->assertTrue($attemptPolicy->interact($student, $attempt));
        $this->assertFalse($attemptPolicy->interact($owner, $attempt));
        $this->assertTrue($attemptPolicy->view($admin, $attempt));
        $this->assertTrue($attemptPolicy->view($owner, $attempt));
        $this->assertTrue($attemptPolicy->reviewProctoring($owner, $attempt));
        $this->assertFalse($attemptPolicy->reviewProctoring($otherTeacher, $attempt));
    }

    /** @test */
    public function security_and_https_middleware_set_expected_response_controls(): void
    {
        $secureHeaders = new SecureHeaders();
        $request = Request::create('/dashboard', 'GET', [], [], [], ['HTTPS' => 'on']);
        $response = $secureHeaders->handle($request, fn () => new Response('OK'));

        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
        $this->assertSame('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
        $this->assertStringContainsString('camera=(self)', $response->headers->get('Permissions-Policy'));
        $this->assertStringContainsString('max-age=31536000', $response->headers->get('Strict-Transport-Security'));

        $forceHttps = new ForceHttps();
        $httpRequest = Request::create('/dashboard', 'GET');
        $redirect = $this->withProductionEnvironment(fn () => $forceHttps->handle(
            $httpRequest,
            fn () => new Response('OK')
        ));

        $this->assertSame(301, $redirect->getStatusCode());
        $this->assertStringStartsWith('https://', $redirect->headers->get('Location'));
    }

    protected function tinyPngDataUri(): string
    {
        return 'data:image/png;base64,' .
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=';
    }

    protected function withProductionEnvironment(callable $callback): mixed
    {
        $app = app();
        $original = $app['env'];
        $app['env'] = 'production';

        try {
            return $callback();
        } finally {
            $app['env'] = $original;
        }
    }
}
