<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentProctoringValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function snapshot_rejects_invalid_base64_payload(): void
    {
        [$student, $attempt] = $this->createStudentAttempt();

        $this->actingAs($student)
            ->postJson(route('student.proctoring.snapshot', $attempt), [
                'snapshot' => '@@@not-base64@@@',
                'violation_type' => 'tab_switch',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['snapshot']);
    }

    /** @test */
    public function heartbeat_requires_boolean_camera_enabled(): void
    {
        [$student, $attempt] = $this->createStudentAttempt();

        $this->actingAs($student)
            ->postJson(route('student.proctoring.heartbeat', $attempt), [
                'camera_enabled' => 'not-boolean',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['camera_enabled']);
    }

    /** @test */
    public function student_cannot_send_proctoring_event_for_other_students_attempt(): void
    {
        [$owner, $attempt] = $this->createStudentAttempt();

        $studentRole = Role::firstOrCreate(['name' => Role::STUDENT], ['display_name' => 'Student']);
        $intruder = User::factory()->create([
            'role_id' => $studentRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $this->actingAs($intruder)
            ->postJson(route('student.proctoring.violation', $attempt), [
                'violation_type' => 'tab_switch',
            ])
            ->assertStatus(403);
    }

    /** @test */
    public function sync_time_logs_large_client_drift_and_returns_remaining_time(): void
    {
        [$student, $attempt] = $this->createStudentAttempt();

        $this->actingAs($student)
            ->postJson(route('student.exams.sync-time', $attempt), [
                'client_time' => now()->subMinutes(2)->timestamp,
            ])
            ->assertOk()
            ->assertJsonStructure([
                'server_time',
                'remaining_time',
                'remaining',
            ]);

        $this->assertDatabaseHas('proctoring_logs', [
            'attempt_id' => $attempt->id,
            'user_id' => $student->id,
            'violation_type' => 'other',
        ]);

        $this->assertSame(1, $attempt->fresh()->violation_count);
    }

    /** @test */
    public function snapshot_upload_with_violation_type_does_not_increment_violation_count(): void
    {
        [$student, $attempt] = $this->createStudentAttempt();

        $snapshot = 'data:image/png;base64,' .
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=';

        $this->actingAs($student)
            ->postJson(route('student.proctoring.snapshot', $attempt), [
                'snapshot' => $snapshot,
                'violation_type' => 'tab_switch',
                'description' => 'Evidence only snapshot',
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'snapshot_stored' => true,
                'should_auto_submit' => false,
                'violation_count' => 0,
            ]);

        $this->assertSame(0, $attempt->fresh()->violation_count);
        $this->assertDatabaseCount('proctoring_logs', 0);
    }

    /**
     * @return array{0: User, 1: ExamAttempt}
     */
    protected function createStudentAttempt(): array
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
            'name' => 'Course Proctoring Validation',
            'code' => 'PRV-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $teacher->id,
        ]);

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Exam Proctoring Validation',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'started_at' => now()->subMinutes(5),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'camera_enabled' => true,
        ]);

        return [$student, $attempt];
    }
}
