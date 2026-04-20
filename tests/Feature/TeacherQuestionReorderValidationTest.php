<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TeacherQuestionReorderValidationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function reorder_rejects_duplicate_question_ids(): void
    {
        [$teacher, $exam, $questions] = $this->createTeacherExamAndQuestions();

        $response = $this->actingAs($teacher)->postJson(
            route('teacher.questions.reorder', $exam),
            [
                'questions' => [$questions[0]->id, $questions[1]->id, $questions[1]->id],
            ]
        );

        $response->assertStatus(422);

        $questions[0]->refresh();
        $questions[1]->refresh();
        $questions[2]->refresh();

        $this->assertSame(1, $questions[0]->order);
        $this->assertSame(2, $questions[1]->order);
        $this->assertSame(3, $questions[2]->order);
    }

    /** @test */
    public function reorder_updates_order_when_payload_is_valid(): void
    {
        [$teacher, $exam, $questions] = $this->createTeacherExamAndQuestions();

        $response = $this->actingAs($teacher)->postJson(
            route('teacher.questions.reorder', $exam),
            [
                'questions' => [$questions[2]->id, $questions[0]->id, $questions[1]->id],
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $questions[0]->refresh();
        $questions[1]->refresh();
        $questions[2]->refresh();

        $this->assertSame(2, $questions[0]->order);
        $this->assertSame(3, $questions[1]->order);
        $this->assertSame(1, $questions[2]->order);
    }

    /**
     * @return array{0: User, 1: Exam, 2: array<int, Question>}
     */
    protected function createTeacherExamAndQuestions(): array
    {
        $teacherRole = Role::firstOrCreate(['name' => Role::TEACHER], ['display_name' => 'Teacher']);

        $teacher = User::factory()->create([
            'role_id' => $teacherRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $course = Course::create([
            'name' => 'Course Reorder Test',
            'code' => 'RDR-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $teacher->id,
        ]);

        $exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Exam Reorder Test',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_DRAFT,
        ]);

        $questions = [
            Question::create([
                'exam_id' => $exam->id,
                'type' => 'essay',
                'question' => 'Question reorder one',
                'points' => 10,
                'order' => 1,
            ]),
            Question::create([
                'exam_id' => $exam->id,
                'type' => 'essay',
                'question' => 'Question reorder two',
                'points' => 10,
                'order' => 2,
            ]),
            Question::create([
                'exam_id' => $exam->id,
                'type' => 'essay',
                'question' => 'Question reorder three',
                'points' => 10,
                'order' => 3,
            ]),
        ];

        return [$teacher, $exam, $questions];
    }
}
