<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show dashboard based on user role
     */
    public function index(): View
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        if ($user->isTeacher()) {
            return $this->teacherDashboard();
        }

        return $this->studentDashboard();
    }

    /**
     * Admin dashboard
     */
    protected function adminDashboard(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::byRole('student')->count(),
            'total_teachers' => User::byRole('teacher')->count(),
            'total_classes' => SchoolClass::count(),
            'total_courses' => Course::count(),
            'total_exams' => Exam::count(),
            'active_exams' => Exam::active()->count(),
            'total_attempts' => ExamAttempt::count(),
            'completed_attempts' => ExamAttempt::submitted()->count(),
            'pending_approvals' => User::pendingApproval()->count(),
        ];

        $recentUsers = User::with('role')
            ->latest()
            ->take(5)
            ->get();

        $recentExams = Exam::with(['course', 'creator'])
            ->latest()
            ->take(5)
            ->get();

        $pendingTeachers = User::with('role')
            ->pendingApproval()
            ->byRole('teacher')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentExams', 'pendingTeachers'));
    }

    /**
     * Teacher dashboard
     */
    protected function teacherDashboard(): View
    {
        $user = auth()->user();

        $courses = $user->taughtCourses()
            ->withCount('students', 'exams')
            ->get();

        $teacherCourseIds = $courses->pluck('id');

        // Show exams the teacher created OR from courses they teach
        $examScope = function ($query) use ($user, $teacherCourseIds) {
            $query->where('created_by', $user->id)
                  ->orWhereIn('course_id', $teacherCourseIds);
        };

        $myExams = Exam::where($examScope)
            ->with('course')
            ->withCount('attempts')
            ->latest()
            ->take(10)
            ->get();

        $activeExams = Exam::where($examScope)
            ->active()
            ->with('course')
            ->withCount('attempts')
            ->get();

        $recentAttempts = ExamAttempt::whereHas('exam', $examScope)
            ->with(['user', 'exam'])
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total_courses' => $courses->count(),
            'total_students' => $courses->sum('students_count'),
            'total_exams' => Exam::where($examScope)->count(),
            'active_exams' => $activeExams->count(),
            'pending_grading' => ExamAttempt::whereHas('exam', $examScope)
                ->where('status', 'submitted')->count(),
        ];

        return view('teacher.dashboard', compact('courses', 'myExams', 'activeExams', 'recentAttempts', 'stats'));
    }

    /**
     * Student dashboard
     */
    protected function studentDashboard(): View
    {
        $user = auth()->user();

        // Get enrolled course IDs for this student
        $enrolledCourseIds = $user->enrolledCourses()->pluck('courses.id');

        // Get exams available for this student's enrolled courses only
        $availableExams = Exam::active()
            ->whereIn('course_id', $enrolledCourseIds)
            ->with('course')
            ->orderBy('start_time')
            ->get();

        $upcomingExams = Exam::upcoming()
            ->whereIn('course_id', $enrolledCourseIds)
            ->with('course')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // Active exams that the student can still work on (new, retry, or continue)
        $activeExams = Exam::active()
            ->whereIn('course_id', $enrolledCourseIds)
            ->with([
                'course',
                'settings',
                'attempts' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)->latest('created_at');
                },
            ])
            ->withCount('questions')
            ->get()
            ->map(function (Exam $exam) {
                $attempts = $exam->attempts;
                $inProgressAttempt = $attempts->first(fn ($attempt) => $attempt->isInProgress());
                $submittedAttempts = $attempts->filter(fn ($attempt) => $attempt->isSubmitted());
                $attemptCount = $submittedAttempts->count();

                // 0 means unlimited attempts.
                $maxAttempts = $exam->settings?->max_attempts ?? 1;
                $canRetry = $maxAttempts === 0 || $attemptCount < $maxAttempts;

                $exam->in_progress_attempt = $inProgressAttempt;
                $exam->attempt_count = $attemptCount;
                $exam->max_attempts = $maxAttempts;
                $exam->can_retry = $canRetry;

                return $exam;
            })
            ->filter(fn (Exam $exam) => $exam->in_progress_attempt !== null || $exam->can_retry)
            ->values();

        $recentResults = ExamAttempt::where('user_id', $user->id)
            ->whereHas('exam') // Only include attempts where exam still exists
            ->with(['exam.course'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $stats = [
            'class_name' => $user->class?->full_name ?? 'Belum ada kelas',
            'upcoming_exams' => $upcomingExams->count(),
            'active_exams' => $activeExams->count(),
            'completed_exams' => $user->examAttempts()->submitted()->count(),
            'average_score' => $user->examAttempts()->submitted()->whereNotNull('score')->avg('percentage') ?? 0,
        ];

        return view('student.dashboard', compact(
            'availableExams',
            'upcomingExams',
            'activeExams',
            'recentResults',
            'stats'
        ));
    }
}
