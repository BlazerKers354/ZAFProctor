<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamSetting;
use App\Models\Role;
use App\Models\User;
use App\Services\ExamService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ViolationThresholdPriorityTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function max_violations_prefers_auto_submit_threshold_when_fields_diverge(): void
    {
        [$student, $exam] = $this->createStudentAndExam();

        ExamSetting::create([
            'exam_id' => $exam->id,
            'max_tab_switches' => 3,
            'auto_submit_threshold' => 7,
        ]);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'started_at' => now()->subMinutes(5),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'violation_count' => 5,
        ]);

        $this->assertSame(7, $attempt->max_violations);
        $this->assertFalse($attempt->hasExceededViolations());

        $attempt->update(['violation_count' => 7]);
        $attempt->refresh();

        $this->assertTrue($attempt->hasExceededViolations());
    }

    /** @test */
    public function exam_service_auto_submit_check_prefers_auto_submit_threshold_when_fields_diverge(): void
    {
        [$student, $exam] = $this->createStudentAndExam();

        ExamSetting::create([
            'exam_id' => $exam->id,
            'max_tab_switches' => 3,
            'auto_submit_threshold' => 7,
        ]);

        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'started_at' => now()->subMinutes(5),
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'violation_count' => 5,
        ]);

        /** @var ExamService $examService */
        $examService = app(ExamService::class);

        $this->assertFalse($examService->shouldAutoSubmitDueToViolations($attempt));

        $attempt->update(['violation_count' => 7]);
        $attempt->refresh();

        $this->assertTrue($examService->shouldAutoSubmitDueToViolations($attempt));
    }

    /**
     * @return array{0: User, 1: Exam}
     */
    protected function createStudentAndExam(): array
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
            'name' => 'Course Threshold Test',
            'code' => 'THR-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $teacher->id,
        ]);

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Exam Threshold Test',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        return [$student, $exam];
    }
}
