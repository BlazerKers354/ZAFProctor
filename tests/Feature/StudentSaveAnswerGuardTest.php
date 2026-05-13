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

class StudentSaveAnswerGuardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function save_answer_returns_json_conflict_when_attempt_is_already_submitted(): void
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
            'name' => 'Course Save Guard',
            'code' => 'SGD-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $teacher->id,
        ]);

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Exam Save Guard',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        $question = Question::create([
            'exam_id' => $exam->id,
            'type' => 'multiple_choice',
            'question' => 'Question save guard test',
            'points' => 10,
            'order' => 1,
        ]);

        $option = QuestionOption::create([
            'question_id' => $question->id,
            'option_label' => 'A',
            'option_text' => 'Option A',
            'is_correct' => true,
            'order' => 1,
        ]);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'started_at' => now()->subHour(),
            'submitted_at' => now()->subMinutes(30),
            'status' => ExamAttempt::STATUS_SUBMITTED,
        ]);

        $this->actingAs($student)
            ->postJson(route('student.exams.save-answer', $attempt), [
                'question_id' => $question->id,
                'option_id' => $option->id,
            ])
            ->assertStatus(409)
            ->assertJson([
                'success' => false,
                'attempt_submitted' => true,
                'redirect' => route('student.exams.result', $attempt->id),
            ]);
    }
}
