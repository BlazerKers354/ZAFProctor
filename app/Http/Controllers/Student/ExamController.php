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
            ->with('course')
            ->orderBy('start_time')
            ->get()
            ->map(function ($exam) use ($user) {
                $attempt = $exam->attempts()->where('user_id', $user->id)->first();
                $exam->user_attempt = $attempt;
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
        $attempt = $exam->attempts()->where('user_id', $user->id)->first();

        return view('student.exams.show', compact('exam', 'attempt'));
    }

    /**
     * Start the exam.
     */
    public function start(Request $request, Exam $exam): RedirectResponse|View
    {
        $this->authorize('start', $exam);

        // Validate access token
        $request->validate([
            'access_token' => ['required', 'string'],
        ]);

        if ($request->access_token !== $exam->access_token) {
            return back()->withErrors(['access_token' => 'Token akses tidak valid.']);
        }

        $user = auth()->user();

        try {
            $attempt = $this->examService->startExam(
                $exam,
                $user->id,
                $request->ip(),
                $request->userAgent()
            );

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
