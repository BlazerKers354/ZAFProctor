<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ProctoringLog;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesZafProctorData;
use Tests\TestCase;

class TeacherWorkflowBlackboxTest extends TestCase
{
    use CreatesZafProctorData;
    use RefreshDatabase;

    /** @test */
    public function teacher_can_create_configure_publish_duplicate_and_export_exam(): void
    {
        $teacher = $this->teacher();
        $course = $this->course($teacher);

        $this->actingAs($teacher)->get(route('teacher.exams.index'))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.exams.create'))->assertOk();

        $this->actingAs($teacher)
            ->post(route('teacher.exams.store'), $this->validExamPayload($course, [
                'access_token' => 'TEACHER123',
            ]))
            ->assertRedirect();

        $exam = Exam::where('access_token', 'TEACHER123')->firstOrFail();
        $this->assertSame(Exam::STATUS_DRAFT, $exam->status);
        $this->assertSame(5, $exam->settings->auto_submit_threshold);

        $this->actingAs($teacher)
            ->post(route('teacher.exams.publish', $exam))
            ->assertRedirect()
            ->assertSessionHas('error');
        $this->assertSame(Exam::STATUS_DRAFT, $exam->fresh()->status);

        $question = $this->multipleChoiceQuestion($exam);

        $this->actingAs($teacher)->get(route('teacher.exams.show', $exam))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.exams.edit', $exam))->assertOk();

        $this->actingAs($teacher)
            ->patch(route('teacher.exams.update-settings', $exam), [
                'snapshot_interval' => 45,
                'webcam_enabled' => '1',
                'browser_lock_enabled' => '1',
                'tab_switch_detection' => '1',
                'block_keyboard_shortcuts' => '1',
                'auto_submit_threshold' => 3,
            ])
            ->assertRedirect();
        $this->assertSame(3, $exam->fresh()->settings->auto_submit_threshold);
        $this->assertSame(3, $exam->fresh()->settings->max_tab_switches);

        $this->actingAs($teacher)
            ->post(route('teacher.exams.publish', $exam))
            ->assertRedirect()
            ->assertSessionHas('success');
        $this->assertSame(Exam::STATUS_PUBLISHED, $exam->fresh()->status);

        $oldToken = $exam->fresh()->access_token;
        $this->actingAs($teacher)
            ->post(route('teacher.exams.regenerate-token', $exam))
            ->assertRedirect();
        $this->assertNotSame($oldToken, $exam->fresh()->access_token);

        $this->actingAs($teacher)
            ->post(route('teacher.exams.duplicate', $exam))
            ->assertRedirect();
        $this->assertDatabaseHas('exams', [
            'title' => $exam->title . ' (Salinan)',
            'status' => Exam::STATUS_DRAFT,
            'created_by' => $teacher->id,
        ]);
        $this->assertSame(2, Exam::where('created_by', $teacher->id)->count());

        $this->actingAs($teacher)
            ->get(route('teacher.exams.export', $exam))
            ->assertOk()
            ->assertHeader('Content-Disposition');

        $this->assertDatabaseHas('questions', ['id' => $question->id]);
    }

    /** @test */
    public function teacher_can_manage_questions_with_crud_bulk_and_csv_flows(): void
    {
        $teacher = $this->teacher();
        $exam = $this->exam($teacher, $this->course($teacher), ['status' => Exam::STATUS_DRAFT]);

        $this->actingAs($teacher)->get(route('teacher.questions.index', $exam))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.questions.create', $exam))->assertOk();

        $this->actingAs($teacher)
            ->post(route('teacher.questions.store', $exam), $this->validQuestionPayload())
            ->assertRedirect(route('teacher.questions.index', $exam, absolute: false));

        $question = $exam->questions()->with('options')->firstOrFail();
        $this->assertSame(Question::TYPE_MULTIPLE_CHOICE, $question->type);
        $this->assertSame(2, $question->options()->count());

        $this->actingAs($teacher)
            ->get(route('teacher.questions.detail', [$exam, $question]))
            ->assertOk()
            ->assertJsonPath('id', $question->id)
            ->assertJsonCount(2, 'options');

        $this->actingAs($teacher)->get(route('teacher.questions.edit', [$exam, $question]))->assertOk();

        $this->actingAs($teacher)
            ->put(route('teacher.questions.update', [$exam, $question]), [
                'question' => 'Which updated fixture answer should be selected?',
                'points' => 15,
                'options' => [
                    ['text' => 'Updated correct'],
                    ['text' => 'Updated wrong'],
                    ['text' => 'Updated wrong two'],
                ],
                'correct_option' => 0,
                'explanation' => 'Updated explanation',
            ])
            ->assertRedirect(route('teacher.questions.index', $exam, absolute: false));
        $this->assertSame(15, $question->fresh()->points);
        $this->assertSame(3, $question->fresh()->options()->count());

        $essay = $this->essayQuestion($exam);
        $ids = $exam->questions()->pluck('id')->reverse()->values()->all();
        $this->actingAs($teacher)
            ->postJson(route('teacher.questions.reorder', $exam), ['questions' => $ids])
            ->assertOk()
            ->assertJson(['success' => true]);
        $this->assertSame(1, $essay->fresh()->order);

        $this->actingAs($teacher)
            ->postJson(route('teacher.questions.duplicate', $exam), ['question_id' => $question->id])
            ->assertOk()
            ->assertJson(['success' => true]);
        $this->assertSame(3, $exam->fresh()->questions()->count());

        $this->actingAs($teacher)
            ->get(route('teacher.questions.download-template', $exam))
            ->assertOk()
            ->assertHeader('Content-Disposition');

        $this->actingAs($teacher)
            ->get(route('teacher.questions.export', $exam))
            ->assertOk()
            ->assertHeader('Content-Disposition');

        $duplicate = $exam->fresh()->questions()->where('question', 'like', '%(Copy)')->firstOrFail();
        $this->actingAs($teacher)
            ->postJson(route('teacher.questions.delete-multiple', $exam), [
                'question_ids' => [$duplicate->id],
            ])
            ->assertOk()
            ->assertJson(['success' => true]);
        $this->assertSoftDeleted('questions', ['id' => $duplicate->id]);

        $this->actingAs($teacher)
            ->deleteJson(route('teacher.questions.destroy', [$exam, $essay]))
            ->assertOk()
            ->assertJson(['success' => true]);
        $this->assertSoftDeleted('questions', ['id' => $essay->id]);
    }

    /** @test */
    public function teacher_can_grade_results_monitor_review_and_terminate_attempts(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $course = $this->course($teacher);
        $this->enroll($student, $course);
        $exam = $this->exam($teacher, $course);
        $essay = $this->essayQuestion($exam, ['points' => 20]);
        $submitted = $this->attempt($exam, $student, [
            'status' => ExamAttempt::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
        $answer = $this->answer($submitted, $essay, ['essay_answer' => 'Essay response']);
        $log = $this->proctoringLog($submitted);

        $this->actingAs($teacher)->get(route('teacher.exams.results', $exam))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.exams.grade', $submitted))->assertOk();

        $this->actingAs($teacher)
            ->post(route('teacher.exams.submit-grade', $submitted), [
                'scores' => [$answer->id => 18],
                'feedback' => 'Strong response',
            ])
            ->assertRedirect(route('teacher.exams.results', $exam, absolute: false));

        $this->assertSame(ExamAttempt::STATUS_GRADED, $submitted->fresh()->status);
        $this->assertSame('18.00', $answer->fresh()->points_earned);

        $this->actingAs($teacher)->get(route('teacher.monitor.index', $exam))->assertOk();
        $this->actingAs($teacher)
            ->getJson(route('teacher.monitor.live', $exam))
            ->assertOk()
            ->assertJsonStructure(['stats', 'active_attempts', 'violations']);
        $this->actingAs($teacher)->get(route('teacher.monitor.attempt', [$exam, $submitted]))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.monitor.logs', [$exam, $submitted]))->assertOk();

        $this->actingAs($teacher)
            ->post(route('teacher.monitor.review-logs', [$exam, $submitted]), [
                'log_ids' => [$log->id],
                'notes' => 'Reviewed from feature test',
            ])
            ->assertRedirect();
        $this->assertTrue($log->fresh()->is_reviewed);

        $active = $this->attempt($exam, $student);
        $this->actingAs($teacher)
            ->post(route('teacher.monitor.terminate', [$exam, $active]))
            ->assertRedirect();
        $this->assertTrue($active->fresh()->isSubmitted());
        $this->assertTrue($active->fresh()->is_auto_submitted);
    }
}
