<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnsureExamInProgressRedirectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_json_request_on_submitted_attempt_redirects_to_result_page(): void
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
            'name' => 'Course Middleware Redirect',
            'code' => 'MRD-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $teacher->id,
        ]);

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Exam Middleware Redirect',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'started_at' => now()->subHour(),
            'submitted_at' => now()->subMinutes(10),
            'status' => ExamAttempt::STATUS_SUBMITTED,
        ]);

        $this->actingAs($student)
            ->post(route('student.exams.submit', $attempt))
            ->assertRedirect(route('student.exams.result', $attempt->id));
    }
}
