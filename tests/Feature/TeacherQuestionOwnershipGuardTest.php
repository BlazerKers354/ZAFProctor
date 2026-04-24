<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TeacherQuestionOwnershipGuardTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function edit_returns_not_found_when_question_does_not_belong_to_exam(): void
    {
        [$teacher, $teacherExam, $foreignQuestion] = $this->createTeacherExamWithForeignQuestion();

        $this->actingAs($teacher)
            ->get(route('teacher.questions.edit', [$teacherExam, $foreignQuestion]))
            ->assertNotFound();
    }

    /** @test */
    public function duplicate_rejects_question_id_outside_exam_scope(): void
    {
        [$teacher, $teacherExam, $foreignQuestion] = $this->createTeacherExamWithForeignQuestion();

        $this->actingAs($teacher)
            ->postJson(route('teacher.questions.duplicate', $teacherExam), [
                'question_id' => $foreignQuestion->id,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['question_id']);
    }

    /** @test */
    public function delete_multiple_rejects_question_ids_from_other_exam(): void
    {
        [$teacher, $teacherExam, $foreignQuestion, $ownedQuestion] = $this->createTeacherExamWithForeignQuestion(true);

        $this->actingAs($teacher)
            ->postJson(route('teacher.questions.delete-multiple', $teacherExam), [
                'question_ids' => [$ownedQuestion->id, $foreignQuestion->id],
            ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('questions', ['id' => $ownedQuestion->id]);
        $this->assertDatabaseHas('questions', ['id' => $foreignQuestion->id]);
    }

    /**
     * @return array{0: User, 1: Exam, 2: Question, 3?: Question}
     */
    protected function createTeacherExamWithForeignQuestion(bool $includeOwnedQuestion = false): array
    {
        $teacherRole = Role::firstOrCreate(['name' => Role::TEACHER], ['display_name' => 'Teacher']);

        $teacher = User::factory()->create([
            'role_id' => $teacherRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $otherTeacher = User::factory()->create([
            'role_id' => $teacherRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $teacherCourse = Course::create([
            'name' => 'Course Teacher Scope',
            'code' => 'TQS-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $teacher->id,
        ]);

        $otherCourse = Course::create([
            'name' => 'Course Foreign Scope',
            'code' => 'FQS-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $otherTeacher->id,
        ]);

        $teacherExam = Exam::create([
            'course_id' => $teacherCourse->id,
            'created_by' => $teacher->id,
            'title' => 'Teacher Exam Scope',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_DRAFT,
        ]);

        $foreignExam = Exam::create([
            'course_id' => $otherCourse->id,
            'created_by' => $otherTeacher->id,
            'title' => 'Foreign Exam Scope',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_DRAFT,
        ]);

        $foreignQuestion = Question::create([
            'exam_id' => $foreignExam->id,
            'type' => 'essay',
            'question' => 'Foreign question for ownership validation',
            'points' => 10,
            'order' => 1,
        ]);

        if (!$includeOwnedQuestion) {
            return [$teacher, $teacherExam, $foreignQuestion];
        }

        $ownedQuestion = Question::create([
            'exam_id' => $teacherExam->id,
            'type' => 'essay',
            'question' => 'Owned question for batch delete validation',
            'points' => 10,
            'order' => 1,
        ]);

        return [$teacher, $teacherExam, $foreignQuestion, $ownedQuestion];
    }
}
