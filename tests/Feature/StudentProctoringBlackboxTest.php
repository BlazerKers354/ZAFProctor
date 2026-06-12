<?php

namespace Tests\Feature;

use App\Models\ExamAttempt;
use App\Models\ProctoringLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Storage;
use Tests\Support\CreatesZafProctorData;
use Tests\TestCase;

class StudentProctoringBlackboxTest extends TestCase
{
    use CreatesZafProctorData;
    use RefreshDatabase;

    /** @test */
    public function student_can_complete_exam_lifecycle_from_listing_to_result(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $course = $this->course($teacher);
        $this->enroll($student, $course);
        $exam = $this->exam($teacher, $course, [
            'access_token' => 'STUDENT123',
        ], [
            'webcam_enabled' => true,
            'show_score' => true,
        ]);
        $question = $this->multipleChoiceQuestion($exam);
        $correctOption = $question->options()->where('is_correct', true)->first();

        $this->actingAs($student)->get(route('student.exams.index'))->assertOk();
        $this->actingAs($student)->get(route('student.exams.show', $exam))->assertOk();
        $this->actingAs($student)->get(route('student.exams.pre-check', $exam))->assertOk();

        $this->actingAs($student)
            ->post(route('student.exams.start', $exam), [
                'access_token' => 'WRONG',
                'pre_check_passed' => '1',
                'camera_verified' => '1',
                'face_verified' => '1',
            ])
            ->assertRedirect(route('student.exams.pre-check', $exam, absolute: false))
            ->assertSessionHasErrors('access_token');

        $this->actingAs($student)
            ->post(route('student.exams.start', $exam), [
                'access_token' => 'STUDENT123',
            ])
            ->assertRedirect(route('student.exams.pre-check', $exam, absolute: false))
            ->assertSessionHasErrors('error');

        $this->actingAs($student)
            ->post(route('student.exams.start', $exam), [
                'access_token' => 'STUDENT123',
                'pre_check_passed' => '1',
                'camera_verified' => '1',
                'face_verified' => '1',
            ])
            ->assertRedirect();

        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', $student->id)
            ->where('status', ExamAttempt::STATUS_IN_PROGRESS)
            ->firstOrFail();

        $this->actingAs($student)->get(route('student.exams.take', $attempt))->assertOk();

        $this->actingAs($student)
            ->postJson(route('student.exams.save-answer', $attempt), [
                'question_id' => $question->id,
                'option_id' => $correctOption->id,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'selected_option_id' => $correctOption->id,
        ]);

        $this->actingAs($student)
            ->postJson(route('student.exams.sync-time', $attempt), [
                'client_time' => now()->timestamp,
            ])
            ->assertOk()
            ->assertJsonStructure(['server_time', 'remaining_time', 'remaining']);

        $this->actingAs($student)
            ->post(route('student.exams.submit', $attempt))
            ->assertRedirect(route('student.exams.result', $attempt, absolute: false));

        $this->assertTrue($attempt->fresh()->isSubmitted());
        $this->assertSame('100.00', $attempt->fresh()->percentage);
        $this->actingAs($student)->get(route('student.exams.result', $attempt))->assertOk();
    }

    /** @test */
    public function student_attempt_limits_and_attempt_ownership_are_enforced(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $otherStudent = $this->student();
        $course = $this->course($teacher);
        $this->enroll($student, $course);
        $exam = $this->exam($teacher, $course, [], ['max_attempts' => 1]);
        $submitted = $this->attempt($exam, $student, [
            'status' => ExamAttempt::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('student.exams.pre-check', $exam))
            ->assertRedirect(route('student.exams.show', $exam, absolute: false))
            ->assertSessionHas('error');

        $this->actingAs($otherStudent)
            ->get(route('student.exams.result', $submitted))
            ->assertForbidden();
    }

    /** @test */
    public function proctoring_endpoints_validate_settings_payloads_thresholds_and_snapshots(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);
        Storage::fake('local');

        $teacher = $this->teacher();
        $student = $this->student();
        $exam = $this->exam($teacher, null, [], [
            'auto_submit_threshold' => 2,
            'max_tab_switches' => 2,
            'webcam_enabled' => true,
            'tab_switch_detection' => true,
            'browser_lock_enabled' => true,
            'block_keyboard_shortcuts' => true,
        ]);
        $attempt = $this->attempt($exam, $student);

        $this->actingAs($student)
            ->getJson(route('student.proctoring.settings', $attempt))
            ->assertOk()
            ->assertJson([
                'require_camera' => true,
                'max_violations' => 2,
            ]);

        $this->actingAs($student)
            ->postJson(route('student.proctoring.violation', $attempt), [
                'violation_type' => 'invalid_type',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('violation_type');

        $exam->settings()->update(['tab_switch_detection' => false]);
        $this->actingAs($student)
            ->postJson(route('student.proctoring.violation', $attempt), [
                'violation_type' => ProctoringLog::TYPE_TAB_SWITCH,
            ])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $exam->settings()->update(['tab_switch_detection' => true]);
        $this->actingAs($student)
            ->postJson(route('student.proctoring.violation', $attempt), [
                'violation_type' => ProctoringLog::TYPE_TAB_SWITCH,
                'description' => 'First violation',
                'metadata' => ['source' => 'feature-test'],
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'violation_count' => 1,
                'should_auto_submit' => false,
            ]);

        $this->actingAs($student)
            ->postJson(route('student.proctoring.violation', $attempt), [
                'violation_type' => ProctoringLog::TYPE_FULLSCREEN_EXIT,
                'description' => 'Second violation',
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'violation_count' => 2,
                'should_auto_submit' => true,
            ]);

        $this->actingAs($student)
            ->postJson(route('student.proctoring.heartbeat', $attempt), [
                'camera_enabled' => true,
            ])
            ->assertStatus(409)
            ->assertJson([
                'attempt_submitted' => true,
                'should_submit' => true,
            ]);
        $this->assertTrue($attempt->fresh()->is_auto_submitted);

        $freshAttempt = $this->attempt($exam, $student);
        $this->actingAs($student)
            ->postJson(route('student.proctoring.heartbeat', $freshAttempt), [
                'camera_enabled' => false,
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'time_expired' => false,
            ]);
        $this->assertFalse($freshAttempt->fresh()->camera_enabled);

        $this->actingAs($student)
            ->postJson(route('student.proctoring.snapshot', $freshAttempt), [
                'snapshot' => '@@@bad@@@',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('snapshot');

        $this->actingAs($student)
            ->postJson(route('student.proctoring.snapshot', $freshAttempt), [
                'snapshot' => $this->tinyPngDataUri(),
                'violation_type' => ProctoringLog::TYPE_TAB_SWITCH,
                'description' => 'Evidence only',
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'snapshot_stored' => true,
                'should_auto_submit' => false,
            ]);
    }

    /** @test */
    public function private_snapshot_access_is_limited_to_authorized_reviewers(): void
    {
        Storage::fake('local');

        $ownerTeacher = $this->teacher();
        $otherTeacher = $this->teacher();
        $student = $this->student();
        $exam = $this->exam($ownerTeacher);
        $attempt = $this->attempt($exam, $student);
        $path = 'proctoring/test/snapshot.png';
        Storage::disk('local')->put($path, base64_decode($this->tinyPngPayload()));
        $log = $this->proctoringLog($attempt, ['snapshot_path' => $path]);

        $this->actingAs($ownerTeacher)
            ->get(route('proctoring.snapshot.view', $log))
            ->assertOk()
            ->assertHeader('Cache-Control');

        $this->actingAs($otherTeacher)
            ->get(route('proctoring.snapshot.view', $log))
            ->assertForbidden();
    }

    protected function tinyPngDataUri(): string
    {
        return 'data:image/png;base64,' . $this->tinyPngPayload();
    }

    protected function tinyPngPayload(): string
    {
        return 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=';
    }
}
