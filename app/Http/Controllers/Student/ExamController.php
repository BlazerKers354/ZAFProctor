<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Services\ExamService;
use App\Services\ProctoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function __construct(
        protected ExamService $examService,
        protected ProctoringService $proctoringService
    ) {}

    /**
     * Display a listing of available exams.
     */
    public function index(): View
    {
        $user = auth()->user();

        $enrolledCourseIds = $user->enrolledCourses()->pluck('courses.id');

        $availableExams = Exam::whereIn('course_id', $enrolledCourseIds)
            ->where('status', Exam::STATUS_PUBLISHED)
            ->with(['course', 'settings'])
            ->orderBy('start_time')
            ->get()
            ->map(function ($exam) use ($user) {
                $attempts = $exam->attempts()->where('user_id', $user->id)->get();
                
                // Get in-progress attempt
                $inProgressAttempt = $attempts->first(fn($a) => $a->isInProgress());
                
                // Get submitted attempts
                $submittedAttempts = $attempts->filter(fn($a) => $a->isSubmitted());
                $attemptCount = $submittedAttempts->count();
                
                // Best attempt
                $bestAttempt = $submittedAttempts->sortByDesc('percentage')->first();
                
                // Check if can retry
                $maxAttempts = $exam->settings?->max_attempts ?? 1;
                $canRetry = $maxAttempts === 0 || $attemptCount < $maxAttempts;
                
                $exam->user_attempt = $inProgressAttempt ?? $bestAttempt;
                $exam->attempt_count = $attemptCount;
                $exam->max_attempts = $maxAttempts;
                $exam->can_retry = $canRetry;
                $exam->best_attempt = $bestAttempt;
                
                return $exam;
            });

        return view('student.exams.index', compact('availableExams'));
    }

    /**
     * Show exam details and preparation page.
     */
    public function show(Exam $exam): View
    {
        $this->authorize('view', $exam);

        $user = auth()->user();
        
        // Get all attempts for this user
        $attempts = $exam->attempts()->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        
        // Get active attempt (in progress) or null
        $attempt = $attempts->first(fn($a) => $a->isInProgress());
        
        // Count submitted attempts
        $submittedAttempts = $attempts->filter(fn($a) => $a->isSubmitted());
        $attemptCount = $submittedAttempts->count();
        
        // Check if can retry
        $maxAttempts = $exam->settings?->max_attempts ?? 1;
        $canRetry = $maxAttempts === 0 || $attemptCount < $maxAttempts;
        
        // Get best attempt for display
        $bestAttempt = $submittedAttempts->sortByDesc('percentage')->first();

        return view('student.exams.show', compact('exam', 'attempt', 'attempts', 'attemptCount', 'canRetry', 'maxAttempts', 'bestAttempt'));
    }

    /**
     * Show pre-exam check page for camera and face verification.
     */
    public function preCheck(Exam $exam): View
    {
        $this->authorize('view', $exam);

        $user = auth()->user();
        $attempts = $exam->attempts()->where('user_id', $user->id)->get();
        
        // Check if has an attempt in progress
        $inProgressAttempt = $attempts->first(fn($a) => $a->isInProgress());
        if ($inProgressAttempt) {
            return redirect()->route('student.exams.take', $inProgressAttempt);
        }

        // Count submitted attempts
        $submittedCount = $attempts->filter(fn($a) => $a->isSubmitted())->count();
        
        // Check if can still attempt
        $maxAttempts = $exam->settings?->max_attempts ?? 1;
        if ($maxAttempts > 0 && $submittedCount >= $maxAttempts) {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'Anda sudah mencapai batas maksimal percobaan.');
        }

        $attempt = null; // New attempt will be created when starting

        return view('student.exams.pre-check', compact('exam', 'attempt'));
    }

    /**
     * Start the exam.
     */
    public function start(Request $request, Exam $exam): RedirectResponse|View
    {
        try {
            $this->authorize('start', $exam);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Redirect back to pre-check with clear error message
            return redirect()->route('student.exams.pre-check', $exam)
                ->withErrors(['error' => 'Anda tidak diizinkan memulai ujian ini. Mungkin Anda sudah mencapai batas maksimal percobaan.'])
                ->with('pre_check_passed', $request->boolean('pre_check_passed'))
                ->with('camera_verified', $request->boolean('camera_verified'))
                ->with('face_verified', $request->boolean('face_verified'));
        }

        // Validate access token and pre-check
        $request->validate([
            'access_token' => ['required', 'string'],
            'pre_check_passed' => ['sometimes', 'boolean'],
            'camera_verified' => ['sometimes', 'boolean'],
            'face_verified' => ['sometimes', 'boolean'],
        ]);

        if ($request->access_token !== $exam->access_token) {
            // Return back to the pre-check page with error and preserve pre-check state
            return redirect()->route('student.exams.pre-check', $exam)
                ->withErrors(['access_token' => 'Token akses tidak valid. Silakan coba lagi.'])
                ->with('pre_check_passed', $request->boolean('pre_check_passed'))
                ->with('camera_verified', $request->boolean('camera_verified'))
                ->with('face_verified', $request->boolean('face_verified'));
        }

        // Check if proctoring is enabled and pre-check was done
        if ($exam->settings?->webcam_enabled) {
            if (!$request->input('camera_verified') && !$request->input('pre_check_passed')) {
                return redirect()->route('student.exams.pre-check', $exam)
                    ->withErrors(['error' => 'Silakan selesaikan verifikasi kamera terlebih dahulu.']);
            }
        }

        $user = auth()->user();

        try {
            $attempt = $this->examService->startExam(
                $exam,
                $user->id,
                $request->ip(),
                $request->userAgent()
            );

            // Mark camera as verified if pre-check passed
            if ($request->input('camera_verified')) {
                $attempt->update(['camera_enabled' => true]);
            }

            return redirect()->route('student.exams.take', $attempt);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Take the exam (exam interface).
     */
    public function take(ExamAttempt $attempt): View
    {
        // Middleware already checks if attempt is valid and in progress
        $attempt->load(['exam.settings', 'answers']);
        
        $questions = $this->examService->getQuestionsForAttempt($attempt->exam, $attempt);

        // Map existing answers
        $answeredQuestions = $attempt->answers->keyBy('question_id');

        return view('student.exams.take', compact('attempt', 'questions', 'answeredQuestions'));
    }

    /**
     * Save an answer (AJAX).
     */
    public function saveAnswer(Request $request, ExamAttempt $attempt): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
            'option_id' => ['nullable', 'integer', 'exists:question_options,id'],
            'essay_answer' => ['nullable', 'string'],
        ]);

        try {
            $answer = $this->examService->saveAnswer(
                $attempt,
                $validated['question_id'],
                $validated['option_id'] ?? null,
                $validated['essay_answer'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Jawaban tersimpan.',
                'answer_id' => $answer->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Submit the exam.
     */
    public function submit(Request $request, ExamAttempt $attempt): RedirectResponse
    {
        $attempt = $this->examService->submitExam($attempt, false);

        return redirect()->route('student.exams.result', $attempt)
            ->with('success', 'Ujian berhasil dikumpulkan.');
    }

    /**
     * Auto-submit the exam (AJAX).
     */
    public function autoSubmit(ExamAttempt $attempt): JsonResponse
    {
        $attempt = $this->examService->submitExam($attempt, true);

        return response()->json([
            'success' => true,
            'message' => 'Ujian dikumpulkan secara otomatis.',
            'redirect' => route('student.exams.result', $attempt),
        ]);
    }

    /**
     * Display exam result.
     */
    public function result(ExamAttempt $attempt): View
    {
        $this->authorize('view', $attempt);

        $attempt->load(['exam.course', 'exam.settings', 'answers.question.options', 'answers.selectedOption']);

        $showAnswers = $attempt->exam->settings?->show_correct_answers ?? false;

        return view('student.exams.result', compact('attempt', 'showAnswers'));
    }

    /**
     * Get remaining time (AJAX).
     */
    public function timeRemaining(ExamAttempt $attempt): JsonResponse
    {
        return response()->json([
            'remaining' => $attempt->remaining_time,
            'expired' => $attempt->hasTimeExpired(),
        ]);
    }

    /**
     * Sync time with server (AJAX) - for time manipulation prevention.
     */
    public function syncTime(Request $request, ExamAttempt $attempt): JsonResponse
    {
        $serverTime = now()->timestamp;
        $clientTime = $request->input('client_time');
        
        // Calculate time drift
        $drift = abs($serverTime - $clientTime);
        
        // If drift is more than 30 seconds, log as suspicious
        if ($drift > 30) {
            $this->proctoringService->logViolation(
                $attempt,
                'other',
                'Suspected time manipulation. Server-client drift: ' . $drift . ' seconds',
                ['drift' => $drift, 'client_time' => $clientTime, 'server_time' => $serverTime]
            );
        }

        return response()->json([
            'server_time' => $serverTime,
            'remaining' => $attempt->remaining_time,
        ]);
    }
}
