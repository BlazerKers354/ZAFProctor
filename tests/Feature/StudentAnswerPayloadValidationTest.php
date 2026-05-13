<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAnswerPayloadValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function save_answer_rejects_essay_payload_for_multiple_choice_question(): void
    {
        [$student, $attempt, $multipleChoiceQuestion, $validOption] = $this->createAttemptWithMixedQuestionTypes();

        $this->actingAs($student)
            ->postJson(route('student.exams.save-answer', $attempt), [
                'question_id' => $multipleChoiceQuestion->id,
                'option_id' => $validOption->id,
                'essay_answer' => 'payload injection',
            ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Payload jawaban tidak valid untuk soal pilihan ganda.',
            ]);

        $this->assertDatabaseMissing('answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $multipleChoiceQuestion->id,
        ]);
    }

    /** @test */
    public function save_answer_rejects_option_payload_for_essay_question(): void
    {
        [$student, $attempt, $essayQuestion, $foreignOption] = $this->createAttemptForEssayPayloadGuard();

        $this->actingAs($student)
            ->postJson(route('student.exams.save-answer', $attempt), [
                'question_id' => $essayQuestion->id,
                'option_id' => $foreignOption->id,
                'essay_answer' => 'Ini seharusnya jawaban esai yang valid.',
            ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Payload jawaban tidak valid untuk soal esai.',
            ]);

        $this->assertDatabaseMissing('answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $essayQuestion->id,
        ]);
    }

    /**
     * @return array{0: User, 1: ExamAttempt, 2: Question, 3: QuestionOption}
     */
    protected function createAttemptWithMixedQuestionTypes(): array
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
            'name' => 'Course Payload Guard MC',
            'code' => 'PGM-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $teacher->id,
        ]);

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Exam Payload Guard MC',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        $multipleChoiceQuestion = Question::create([
            'exam_id' => $exam->id,
            'type' => Question::TYPE_MULTIPLE_CHOICE,
            'question' => 'Soal pilihan ganda untuk guard payload',
            'points' => 10,
            'order' => 1,
        ]);

        $validOption = QuestionOption::create([
            'question_id' => $multipleChoiceQuestion->id,
            'option_label' => 'A',
            'option_text' => 'Jawaban A',
            'is_correct' => true,
            'order' => 1,
        ]);

        Question::create([
            'exam_id' => $exam->id,
            'type' => Question::TYPE_ESSAY,
            'question' => 'Soal esai sampingan',
            'points' => 10,
            'order' => 2,
        ]);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'started_at' => now()->subMinutes(5),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
        ]);

        return [$student, $attempt, $multipleChoiceQuestion, $validOption];
    }

    /**
     * @return array{0: User, 1: ExamAttempt, 2: Question, 3: QuestionOption}
     */
    protected function createAttemptForEssayPayloadGuard(): array
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
            'name' => 'Course Payload Guard Essay',
            'code' => 'PGE-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $teacher->id,
        ]);

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Exam Payload Guard Essay',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        $essayQuestion = Question::create([
            'exam_id' => $exam->id,
            'type' => Question::TYPE_ESSAY,
            'question' => 'Soal esai untuk guard payload',
            'points' => 10,
            'order' => 1,
        ]);

        $multipleChoiceQuestion = Question::create([
            'exam_id' => $exam->id,
            'type' => Question::TYPE_MULTIPLE_CHOICE,
            'question' => 'Soal pilihan ganda lain',
            'points' => 10,
            'order' => 2,
        ]);

        $foreignOption = QuestionOption::create([
            'question_id' => $multipleChoiceQuestion->id,
            'option_label' => 'A',
            'option_text' => 'Pilihan dari soal lain',
            'is_correct' => true,
            'order' => 1,
        ]);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'started_at' => now()->subMinutes(5),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
        ]);

        return [$student, $attempt, $essayQuestion, $foreignOption];
    }
}
