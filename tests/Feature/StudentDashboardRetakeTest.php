<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamSetting;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardRetakeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dashboard_shows_retry_when_max_attempts_is_changed_to_unlimited(): void
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
            'name' => 'Matematika Dasar',
            'code' => 'MATH-' . strtoupper(substr(uniqid(), -6)),
            'teacher_id' => $teacher->id,
        ]);

        $course->students()->attach($student->id, ['enrolled_at' => now()]);

        $exam = Exam::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Ujian Retry Unlimited',
            'type' => 'flexible',
            'duration' => 60,
            'status' => Exam::STATUS_PUBLISHED,
        ]);

        ExamSetting::create([
            'exam_id' => $exam->id,
            'max_attempts' => 1,
            'max_tab_switches' => 5,
            'auto_submit_threshold' => 5,
        ]);

        ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'started_at' => now()->subHour(),
            'submitted_at' => now()->subMinutes(30),
            'status' => ExamAttempt::STATUS_SUBMITTED,
            'score' => 70,
            'percentage' => 70,
            'is_passed' => true,
        ]);

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('activeExams', function ($activeExams) use ($exam) {
                return $activeExams->firstWhere('id', $exam->id) === null;
            });

        $exam->settings()->update(['max_attempts' => 0]);

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('activeExams', function ($activeExams) use ($exam) {
                $examOnDashboard = $activeExams->firstWhere('id', $exam->id);

                return $examOnDashboard !== null
                    && (bool) ($examOnDashboard->can_retry ?? false)
                    && (int) ($examOnDashboard->attempt_count ?? 0) === 1
                    && (int) ($examOnDashboard->max_attempts ?? -1) === 0;
            })
            ->assertSeeText('Coba Lagi');
    }
}
