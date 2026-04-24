<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Services\ExamService;
use App\Services\ProctoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MonitorController extends Controller
{
    public function __construct(
        protected ExamService $examService,
        protected ProctoringService $proctoringService
    ) {}

    /**
     * Display the monitoring dashboard for an exam.
     */
    public function index(Exam $exam): View
    {
        $this->authorize('monitor', $exam);

        $exam->load(['course', 'questions']);

        $attemptsQuery = ExamAttempt::where('exam_id', $exam->id);
        
        $activeAttempts = (clone $attemptsQuery)
            ->where('status', ExamAttempt::STATUS_IN_PROGRESS)
            ->with(['user', 'latestSnapshot'])
            ->withCount('answers')
            ->latest('started_at')
            ->get();
        
        $submittedCount = (clone $attemptsQuery)
            ->whereIn('status', [ExamAttempt::STATUS_SUBMITTED, ExamAttempt::STATUS_GRADED])
            ->count();
        
        $totalViolations = (clone $attemptsQuery)->sum('violation_count');
        $notStartedCount = (clone $attemptsQuery)
            ->where('status', ExamAttempt::STATUS_NOT_STARTED)
            ->count();

        // Paginated list for display
        $attempts = (clone $attemptsQuery)
            ->with(['user', 'latestSnapshot', 'proctoringLogs' => function ($query) {
                $query->latest()->take(5);
            }])
            ->latest('started_at')
            ->paginate(20);

        // Recent violations
        $recentViolations = \App\Models\ProctoringLog::whereHas('attempt', function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            })
            ->with('attempt.user')
            ->latest()
            ->take(10)
            ->get();

        $statistics = $this->examService->getExamStatistics($exam);

        $statusCounts = [
            'not_started' => $notStartedCount,
            'in_progress' => $activeAttempts->count(),
            'submitted' => $submittedCount,
        ];

        return view('teacher.monitor.index', compact(
            'exam', 
            'attempts', 
            'activeAttempts',
            'submittedCount',
            'totalViolations',
            'recentViolations',
            'statistics', 
            'statusCounts'
        ));
    }

    /**
     * Live AJAX data for monitoring dashboard (no full reload).
     */
    public function liveData(Exam $exam): \Illuminate\Http\JsonResponse
    {
        $this->authorize('monitor', $exam);

        $exam->load('questions');

        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->with(['user', 'proctoringLogs' => fn($q) => $q->latest()->take(5)])
            ->withCount('answers')
            ->get();

        $activeAttempts = $attempts->where('status', ExamAttempt::STATUS_IN_PROGRESS)->values();
        $questionCount = $exam->questions->count();

        $recentViolations = \App\Models\ProctoringLog::whereIn('attempt_id', $attempts->pluck('id'))
            ->with('attempt.user')
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'stats' => [
                'total' => $attempts->count(),
                'in_progress' => $attempts->where('status', ExamAttempt::STATUS_IN_PROGRESS)->count(),
                'submitted' => $attempts->whereIn('status', [ExamAttempt::STATUS_SUBMITTED, ExamAttempt::STATUS_GRADED])->count(),
                'violations' => $attempts->sum('violation_count'),
            ],
            'active_attempts' => $activeAttempts->map(fn($a) => [
                'id' => $a->id,
                'name' => $a->user->name,
                'email' => $a->user->email,
                'violation_count' => $a->violation_count,
                'camera_enabled' => $a->camera_enabled,
                'answers_count' => $a->answers_count,
                'question_count' => $questionCount,
                'remaining_time' => $a->remaining_time,
                'monitor_url' => route('teacher.monitor.attempt', [$exam, $a]),
                'logs_url' => route('teacher.monitor.logs', [$exam, $a]),
            ]),
            'violations' => $recentViolations->map(fn($v) => [
                'id' => $v->id,
                'type' => $v->violation_type,
                'description' => $v->description,
                'user_name' => $v->attempt?->user?->name ?? '-',
                'time_ago' => $v->created_at->diffForHumans(),
                'snapshot_url' => $v->snapshot_path ? route('proctoring.snapshot.view', $v->id) : null,
            ]),
        ]);
    }

    /**
     * View a specific attempt's details.
     */
    public function attempt(Exam $exam, ExamAttempt $attempt): View
    {
        $this->authorize('monitor', $exam);
        $this->ensureAttemptBelongsToExam($exam, $attempt);

        $attempt->load(['user', 'answers.question', 'answers.selectedOption']);
        
        $violationSummary = $this->proctoringService->getViolationSummary($attempt);
        $cameraStats = $this->proctoringService->checkCameraConsistency($attempt);
        $snapshots = $this->proctoringService->getSnapshots($attempt);

        return view('teacher.monitor.attempt', compact(
            'exam',
            'attempt',
            'violationSummary',
            'cameraStats',
            'snapshots'
        ));
    }

    /**
     * View proctoring logs for an attempt.
     */
    public function logs(Exam $exam, ExamAttempt $attempt): View
    {
        $this->authorize('monitor', $exam);
        $this->ensureAttemptBelongsToExam($exam, $attempt);

        $logs = $attempt->proctoringLogs()
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('teacher.monitor.logs', compact('exam', 'attempt', 'logs'));
    }

    /**
     * Mark proctoring logs as reviewed.
     */
    public function reviewLogs(Request $request, Exam $exam, ExamAttempt $attempt): RedirectResponse
    {
        $this->authorize('monitor', $exam);
        $this->ensureAttemptBelongsToExam($exam, $attempt);

        $validated = $request->validate([
            'log_ids' => ['required', 'array'],
            'log_ids.*' => [
                'integer',
                Rule::exists('proctoring_logs', 'id')->where(function ($query) use ($attempt) {
                    $query->where('attempt_id', $attempt->id);
                }),
            ],
            'notes' => ['nullable', 'string'],
        ]);

        $this->proctoringService->markAsReviewed(
            $validated['log_ids'],
            Auth::id(),
            $validated['notes'] ?? null
        );

        return back()->with('success', 'Log berhasil ditandai sebagai telah direview.');
    }

    /**
     * Grade essay answers for an attempt.
     */
    public function gradeEssay(Request $request, Exam $exam, ExamAttempt $attempt): RedirectResponse
    {
        $this->authorize('monitor', $exam);
        $this->ensureAttemptBelongsToExam($exam, $attempt);

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*.id' => [
                'required',
                'integer',
                Rule::exists('answers', 'id')->where(function ($query) use ($attempt) {
                    $query->where('attempt_id', $attempt->id);
                }),
            ],
            'answers.*.points' => ['required', 'numeric', 'min:0'],
            'answers.*.feedback' => ['nullable', 'string'],
        ]);

        foreach ($validated['answers'] as $answerData) {
            $answer = Answer::find($answerData['id']);
            
            if ($answer && $answer->attempt_id === $attempt->id) {
                $this->examService->gradeEssayAnswer(
                    $answer,
                    $answerData['points'],
                    $answerData['feedback'] ?? null
                );
            }
        }

        // Update attempt status to graded
        $attempt->update(['status' => ExamAttempt::STATUS_GRADED]);

        return back()->with('success', 'Penilaian esai berhasil disimpan.');
    }

    /**
     * Terminate a student's exam attempt.
     */
    public function terminate(Request $request, Exam $exam, ExamAttempt $attempt): RedirectResponse
    {
        $this->authorize('monitor', $exam);
        $this->ensureAttemptBelongsToExam($exam, $attempt);

        if (!$attempt->isInProgress()) {
            return back()->with('error', 'Attempt ini sudah tidak berlangsung.');
        }

        // Use ExamService to submit the exam
        $this->examService->submitExam($attempt, true);

        return back()->with('success', 'Ujian peserta berhasil dihentikan.');
    }

    /**
     * Export results to CSV.
     */
    public function exportResults(Exam $exam)
    {
        $this->authorize('monitor', $exam);

        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->submitted()
            ->with('user')
            ->get();

        $filename = "hasil_ujian_{$exam->id}_" . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($attempts) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'No',
                'NIM',
                'Nama',
                'Mulai',
                'Selesai',
                'Skor',
                'Persentase',
                'Status Lulus',
                'Jumlah Pelanggaran',
                'Auto Submit',
            ]);

            // Data
            foreach ($attempts as $index => $attempt) {
                fputcsv($file, [
                    $index + 1,
                    $attempt->user->student_id,
                    $attempt->user->name,
                    $attempt->started_at?->format('Y-m-d H:i:s'),
                    $attempt->submitted_at?->format('Y-m-d H:i:s'),
                    $attempt->score,
                    $attempt->percentage . '%',
                    $attempt->is_passed ? 'Lulus' : 'Tidak Lulus',
                    $attempt->violation_count,
                    $attempt->is_auto_submitted ? 'Ya' : 'Tidak',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Ensure attempt belongs to the exam in the current route.
     */
    protected function ensureAttemptBelongsToExam(Exam $exam, ExamAttempt $attempt): void
    {
        if ((int) $attempt->exam_id !== (int) $exam->id) {
            abort(404, 'Attempt tidak ditemukan pada ujian ini.');
        }
    }
}
