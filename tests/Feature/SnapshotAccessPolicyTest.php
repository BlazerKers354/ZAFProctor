<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ProctoringLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SnapshotAccessPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function exam_owner_teacher_can_view_snapshot(): void
    {
        [$teacher, $log] = $this->createSnapshotData();

        $response = $this->actingAs($teacher)
            ->get(route('proctoring.snapshot.view', $log));

        $response->assertOk();

        $cacheControl = strtolower((string) $response->headers->get('Cache-Control'));

        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('private', $cacheControl);
    }

    /** @test */
    public function non_owner_teacher_cannot_view_snapshot(): void
    {
        [$ownerTeacher, $log] = $this->createSnapshotData();

        $teacherRole = Role::firstOrCreate(['name' => Role::TEACHER], ['display_name' => 'Teacher']);
        $otherTeacher = User::factory()->create([
            'role_id' => $teacherRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $this->actingAs($otherTeacher)
            ->get(route('proctoring.snapshot.view', $log))
            ->assertStatus(403);
    }

    /**
     * @return array{0: User, 1: ProctoringLog}
     */
    protected function createSnapshotData(): array
    {
        Storage::disk('local')->put('proctoring/test/snapshot.jpg', 'dummy-image-content');

        $teacherRole = Role::firstOrCreate(['name' => Role::TEACHER], ['display_name' => 'Teacher']);
        $studentRole = Role::firstOrCreate(['name' => Role::STUDENT], ['display_name' => 'Student']);

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
            'name' => 'Course Snapshot Policy',
            'code' => 'SNP-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $teacher->id,
        ]);

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Exam Snapshot Policy',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'started_at' => now()->subMinutes(10),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
        ]);

        $log = ProctoringLog::create([
            'attempt_id' => $attempt->id,
            'user_id' => $student->id,
            'violation_type' => ProctoringLog::TYPE_TAB_SWITCH,
            'description' => 'Snapshot policy test',
            'snapshot_path' => 'proctoring/test/snapshot.jpg',
            'severity' => ProctoringLog::SEVERITY_HIGH,
        ]);

        return [$teacher, $log];
    }
}
