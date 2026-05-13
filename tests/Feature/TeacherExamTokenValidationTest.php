<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exam;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherExamTokenValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function teacher_exam_store_rejects_access_token_longer_than_database_column(): void
    {
        [$teacher, $course] = $this->createTeacherAndCourse();

        $this->actingAs($teacher)
            ->post(route('teacher.exams.store'), $this->validPayload($course, [
                'access_token' => str_repeat('A', 33),
            ]))
            ->assertSessionHasErrors('access_token');

        $this->assertDatabaseMissing('exams', [
            'title' => 'Token Validation Exam',
        ]);
    }

    /** @test */
    public function teacher_exam_store_rejects_duplicate_access_token(): void
    {
        [$teacher, $course] = $this->createTeacherAndCourse();

        Exam::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Existing Token Exam',
            'type' => 'flexible',
            'duration' => 60,
            'access_token' => 'DUPLICATE123',
            'status' => Exam::STATUS_DRAFT,
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.exams.store'), $this->validPayload($course, [
                'access_token' => 'DUPLICATE123',
            ]))
            ->assertSessionHasErrors('access_token');
    }

    /** @test */
    public function route_list_still_boots_after_dead_code_cleanup(): void
    {
        $this->artisan('route:list')->assertExitCode(0);
    }

    /**
     * @return array{0: User, 1: Course}
     */
    protected function createTeacherAndCourse(): array
    {
        $teacherRole = Role::firstOrCreate(['name' => Role::TEACHER], ['display_name' => 'Teacher']);

        $teacher = User::factory()->create([
            'role_id' => $teacherRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $course = Course::create([
            'name' => 'Token Validation Course',
            'code' => 'TV-' . strtoupper(substr(uniqid(), -8)),
            'teacher_id' => $teacher->id,
        ]);

        return [$teacher, $course];
    }

    protected function validPayload(Course $course, array $overrides = []): array
    {
        return array_merge([
            'course_id' => $course->id,
            'title' => 'Token Validation Exam',
            'description' => 'Exam used for token validation tests.',
            'type' => 'flexible',
            'duration' => 60,
            'access_token' => 'VALID123',
            'status' => Exam::STATUS_DRAFT,
            'max_attempts' => 1,
            'max_tab_switches' => 5,
            'passing_score' => 60,
            'grade_method' => 'highest',
        ], $overrides);
    }
}
