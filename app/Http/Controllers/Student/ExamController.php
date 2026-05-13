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
            ->with(['course', 'settings', 'attempts' => fn($q) => $q->where('user_id', $user->id)])
            ->orderBy('start_time')
            ->get()
            ->map(function ($exam) use ($user) {
                $attempts = $exam->attempts;
                
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
                $proctoringRequirements = $this->buildProctoringRequirements($exam->settings);
                
                $exam->user_attempt = $inProgressAttempt ?? $bestAttempt;
                $exam->attempt_count = $attemptCount;
                $exam->max_attempts = $maxAttempts;
                $exam->can_retry = $canRetry;
                $exam->best_attempt = $bestAttempt;
                $exam->proctoring_requirements = $proctoringRequirements;
                $exam->has_proctoring_requirements = !empty($proctoringRequirements);
                
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
        $exam->load(['settings', 'attempts' => fn($q) => $q->where('user_id', $user->id)->orderBy('created_at', 'desc')]);
        $attempts = $exam->attempts;
        
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

        $proctoringRequirements = $this->buildProctoringRequirements($exam->settings);
        $hasProctoringRequirements = !empty($proctoringRequirements);

        return view('student.exams.show', compact(
            'exam',
            'attempt',
            'attempts',
            'attemptCount',
            'canRetry',
            'maxAttempts',
            'bestAttempt',
            'proctoringRequirements',
            'hasProctoringRequirements'
        ));
    }

    /**
     * Show pre-exam check page for camera and face verification.
     */
    public function preCheck(Exam $exam): View|RedirectResponse
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

        $providedToken = (string) $request->input('access_token', '');
        $expectedToken = (string) ($exam->access_token ?? '');

        if (!hash_equals($expectedToken, $providedToken)) {
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
            \Illuminate\Support\Facades\Log::error('Failed to start exam: ' . $e->getMessage(), [
                'exam_id' => $exam->id,
                'user_id' => $user->id,
            ]);
            
            // Don't expose internal error details to user
            return back()->withErrors(['error' => 'Gagal memulai ujian. Silakan coba lagi atau hubungi administrator.']);
        }
    }

    /**
     * Take the exam (exam interface).
     */
    public function take(ExamAttempt $attempt): View
    {
        $this->authorize('interact', $attempt);

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
        $this->authorize('interact', $attempt);

        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
            'option_id' => ['nullable', 'integer', 'exists:question_options,id'],
            'essay_answer' => ['nullable', 'string', 'max:10000'],
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
            $freshAttempt = $attempt->fresh();
            if ($freshAttempt && $freshAttempt->isSubmitted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ujian sudah dikumpulkan. Anda akan diarahkan ke halaman hasil.',
                    'attempt_submitted' => true,
                    'redirect' => route('student.exams.result', $freshAttempt),
                ], 409);
            }

            $domainValidationMessages = [
                'Soal tidak valid untuk ujian ini.',
                'Pilihan jawaban wajib diisi untuk soal pilihan ganda.',
                'Pilihan jawaban tidak valid untuk soal ini.',
                'Payload jawaban tidak valid untuk soal pilihan ganda.',
                'Payload jawaban tidak valid untuk soal esai.',
                'Jawaban esai melebihi batas maksimal karakter.',
            ];

            if (in_array($e->getMessage(), $domainValidationMessages, true)) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            \Illuminate\Support\Facades\Log::error('Failed to save answer: ' . $e->getMessage(), [
                'attempt_id' => $attempt->id,
                'question_id' => $validated['question_id'] ?? null,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan jawaban. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Submit the exam.
     */
    public function submit(Request $request, ExamAttempt $attempt): RedirectResponse
    {
        $this->authorize('interact', $attempt);

        try {
            $attempt = $this->examService->submitExam($attempt, false);

            return redirect()->route('student.exams.result', $attempt)
                ->with('success', 'Ujian berhasil dikumpulkan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to submit exam: ' . $e->getMessage(), [
                'attempt_id' => $attempt->id,
            ]);
            
            return back()->withErrors(['error' => 'Gagal mengumpulkan ujian. Silakan coba lagi.']);
        }
    }

    /**
     * Auto-submit the exam (AJAX).
     */
    public function autoSubmit(ExamAttempt $attempt): JsonResponse
    {
        $this->authorize('interact', $attempt);

        try {
            $attempt = $this->examService->submitExam($attempt, true);

            return response()->json([
                'success' => true,
                'message' => 'Ujian dikumpulkan secara otomatis.',
                'redirect' => route('student.exams.result', $attempt),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to auto-submit exam: ' . $e->getMessage(), [
                'attempt_id' => $attempt->id,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengumpulkan ujian secara otomatis.',
            ], 500);
        }
    }

    /**
     * Display exam result.
     */
    public function result(ExamAttempt $attempt): View
    {
        $this->authorize('view', $attempt);

        $attempt->load(['exam.course', 'exam.settings', 'answers.question.options', 'answers.selectedOption']);

        $showAnswers = $attempt->exam->settings?->show_correct_answers ?? false;
        $maxViolations = $this->resolveMaxViolationsFromSettings($attempt->exam->settings);

        return view('student.exams.result', compact('attempt', 'showAnswers', 'maxViolations'));
    }

    /**
     * Sync time with server (AJAX) - for time manipulation prevention.
     */
    public function syncTime(Request $request, ExamAttempt $attempt): JsonResponse
    {
        $this->authorize('interact', $attempt);

        $validated = $request->validate([
            'client_time' => ['nullable', 'integer', 'min:0', 'max:4102444800'],
        ]);

        try {
            $serverTime = now()->timestamp;
            $clientTime = $validated['client_time'] ?? null;
            
            // Calculate time drift
            $drift = abs($serverTime - ($clientTime ?? $serverTime));
            
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
                'remaining_time' => $attempt->remaining_time ?? 0,
                'remaining' => $attempt->remaining_time ?? 0,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to sync time: ' . $e->getMessage(), [
                'attempt_id' => $attempt->id,
            ]);
            
            return response()->json([
                'server_time' => now()->timestamp,
                'remaining_time' => $attempt->remaining_time ?? 0,
                'remaining' => $attempt->remaining_time ?? 0,
            ]);
        }
    }

    /**
     * Build student-facing proctoring requirements dynamically from exam settings.
     */
    protected function buildProctoringRequirements($settings): array
    {
        if (!$settings) {
            return [];
        }

        $requirements = [];
        $maxViolations = $this->resolveMaxViolationsFromSettings($settings);
        $hasViolationMonitoring = false;

        if ($settings->webcam_enabled) {
            $requirements[] = 'Kamera wajib aktif selama ujian berlangsung.';
            $hasViolationMonitoring = true;

            if (is_numeric($settings->snapshot_interval) && (int) $settings->snapshot_interval > 0) {
                $requirements[] = 'Snapshot kamera diambil setiap ' . (int) $settings->snapshot_interval . ' detik.';
            }
        }

        if ($settings->browser_lock_enabled) {
            $requirements[] = 'Mode fullscreen wajib selama ujian.';
            $hasViolationMonitoring = true;
        }

        if ($settings->tab_switch_detection) {
            $requirements[] = 'Pindah tab/aplikasi dihitung sebagai pelanggaran.';
            $hasViolationMonitoring = true;
        }

        if ($settings->block_keyboard_shortcuts) {
            $requirements[] = 'Copy/paste, klik kanan, dan shortcut terlarang diblokir.';
            $hasViolationMonitoring = true;
        }

        if ($hasViolationMonitoring) {
            $requirements[] = 'Peringatan pelanggaran ditampilkan sejak pelanggaran pertama.';

            if (is_numeric($maxViolations)) {
                $maxViolations = (int) $maxViolations;

                if ($maxViolations > 0) {
                    $requirements[] = 'Batas total pelanggaran adalah ' . $maxViolations . ' sebelum ujian otomatis dikumpulkan.';
                } else {
                    $requirements[] = 'Pelanggaran tetap dicatat tanpa auto-submit berdasarkan jumlah pelanggaran.';
                }
            }
        }

        return $requirements;
    }

    /**
     * Resolve violation threshold from settings.
     * Returns null when threshold is not explicitly configured or is unlimited (0).
     */
    protected function resolveMaxViolationsFromSettings($settings): ?int
    {
        if (!$settings) {
            return null;
        }

        return $settings->resolveViolationLimit();
    }
}
