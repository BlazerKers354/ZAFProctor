<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StudentSaveAnswerOptionIntegrityTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function save_answer_rejects_option_that_does_not_belong_to_question(): void
    {
        [$student, $attempt, $questionA, $foreignOption] = $this->createExamAttemptWithTwoQuestions();

        $this->actingAs($student)
            ->postJson(route('student.exams.save-answer', $attempt), [
                'question_id' => $questionA->id,
                'option_id' => $foreignOption->id,
            ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Pilihan jawaban tidak valid untuk soal ini.',
            ]);

        $this->assertDatabaseMissing('answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $questionA->id,
        ]);
    }

    /**
     * @return array{0: User, 1: ExamAttempt, 2: Question, 3: QuestionOption}
     */
    protected function createExamAttemptWithTwoQuestions(): array
    {
        $studentRole = Role::firstOrCreate(['name' => Role::STUDENT], ['display_name' => 'Student']);
        $teacherRole = Role::firstOrCreate(['name' => Role::TEACHER], ['display_name' => 'Teacher']);

        $teacher = User::factory()->create([
            'role_id' => $teacherRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $student = User::factory()->create([
            'role_id' => $studentRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $course = Course::create([
            'name' => 'Course Option Integrity',
            'code' => 'OPT-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $teacher->id,
        ]);

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Exam Option Integrity',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        $questionA = Question::create([
            'exam_id' => $exam->id,
            'type' => Question::TYPE_MULTIPLE_CHOICE,
            'question' => 'Question A option integrity validation',
            'points' => 10,
            'order' => 1,
        ]);

        $questionB = Question::create([
            'exam_id' => $exam->id,
            'type' => Question::TYPE_MULTIPLE_CHOICE,
            'question' => 'Question B option integrity validation',
            'points' => 10,
            'order' => 2,
        ]);

        QuestionOption::create([
            'question_id' => $questionA->id,
            'option_label' => 'A',
            'option_text' => 'Question A option',
            'is_correct' => true,
            'order' => 1,
        ]);

        $foreignOption = QuestionOption::create([
            'question_id' => $questionB->id,
            'option_label' => 'A',
            'option_text' => 'Question B option',
            'is_correct' => true,
            'order' => 1,
        ]);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'started_at' => now()->subMinutes(5),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
        ]);

        return [$student, $attempt, $questionA, $foreignOption];
    }
}
