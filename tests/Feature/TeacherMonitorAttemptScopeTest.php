<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ProctoringLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TeacherMonitorAttemptScopeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function attempt_page_returns_not_found_for_attempt_outside_exam(): void
    {
        [$teacher, $teacherExam, $foreignAttempt] = $this->createMonitorScopeData();

        $this->actingAs($teacher)
            ->get(route('teacher.monitor.attempt', [$teacherExam, $foreignAttempt]))
            ->assertNotFound();
    }

    /** @test */
    public function review_logs_rejects_log_ids_from_other_attempt(): void
    {
        [$teacher, $teacherExam, $teacherAttempt, $teacherAttemptLog, $otherAttemptLog] = $this->createMonitorReviewData();

        $response = $this->actingAs($teacher)
            ->postJson(route('teacher.monitor.review-logs', [$teacherExam, $teacherAttempt]), [
                'log_ids' => [$teacherAttemptLog->id, $otherAttemptLog->id],
                'notes' => 'Review scope test',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['log_ids.1']);

        $this->assertDatabaseHas('proctoring_logs', [
            'id' => $teacherAttemptLog->id,
            'is_reviewed' => false,
        ]);

        $this->assertDatabaseHas('proctoring_logs', [
            'id' => $otherAttemptLog->id,
            'is_reviewed' => false,
        ]);
    }

    /**
     * @return array{0: User, 1: Exam, 2: ExamAttempt}
     */
    protected function createMonitorScopeData(): array
    {
        $teacherRole = Role::firstOrCreate(['name' => Role::TEACHER], ['display_name' => 'Teacher']);
        $studentRole = Role::firstOrCreate(['name' => Role::STUDENT], ['display_name' => 'Student']);

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

        $student = User::factory()->create([
            'role_id' => $studentRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $teacherCourse = Course::create([
            'name' => 'Monitor Scope Teacher Course',
            'code' => 'MSC-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $teacher->id,
        ]);

        $otherCourse = Course::create([
            'name' => 'Monitor Scope Foreign Course',
            'code' => 'MFC-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $otherTeacher->id,
        ]);

        $teacherExam = Exam::create([
            'course_id' => $teacherCourse->id,
            'created_by' => $teacher->id,
            'title' => 'Teacher Monitor Exam',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        $otherExam = Exam::create([
            'course_id' => $otherCourse->id,
            'created_by' => $otherTeacher->id,
            'title' => 'Foreign Monitor Exam',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        $foreignAttempt = ExamAttempt::create([
            'exam_id' => $otherExam->id,
            'user_id' => $student->id,
            'started_at' => now()->subMinutes(20),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
        ]);

        return [$teacher, $teacherExam, $foreignAttempt];
    }

    /**
     * @return array{0: User, 1: Exam, 2: ExamAttempt, 3: ProctoringLog, 4: ProctoringLog}
     */
    protected function createMonitorReviewData(): array
    {
        $teacherRole = Role::firstOrCreate(['name' => Role::TEACHER], ['display_name' => 'Teacher']);
        $studentRole = Role::firstOrCreate(['name' => Role::STUDENT], ['display_name' => 'Student']);

        $teacher = User::factory()->create([
            'role_id' => $teacherRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $studentA = User::factory()->create([
            'role_id' => $studentRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $studentB = User::factory()->create([
            'role_id' => $studentRole->id,
            'is_active' => true,
            'is_approved' => true,
        ]);

        $course = Course::create([
            'name' => 'Monitor Review Scope Course',
            'code' => 'MRS-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $teacher->id,
        ]);

        $exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Monitor Review Scope Exam',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        $attemptA = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $studentA->id,
            'started_at' => now()->subMinutes(10),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
        ]);

        $attemptB = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $studentB->id,
            'started_at' => now()->subMinutes(8),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
        ]);

        $logA = ProctoringLog::create([
            'attempt_id' => $attemptA->id,
            'user_id' => $studentA->id,
            'violation_type' => ProctoringLog::TYPE_WINDOW_BLUR,
            'description' => 'Attempt A violation',
            'severity' => ProctoringLog::SEVERITY_MEDIUM,
            'is_reviewed' => false,
        ]);

        $logB = ProctoringLog::create([
            'attempt_id' => $attemptB->id,
            'user_id' => $studentB->id,
            'violation_type' => ProctoringLog::TYPE_TAB_SWITCH,
            'description' => 'Attempt B violation',
            'severity' => ProctoringLog::SEVERITY_HIGH,
            'is_reviewed' => false,
        ]);

        return [$teacher, $exam, $attemptA, $logA, $logB];
    }
}
