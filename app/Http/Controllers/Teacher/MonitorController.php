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

        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->with(['user', 'latestSnapshot', 'proctoringLogs' => function ($query) {
                $query->latest()->take(5);
            }])
            ->get();

        // Active attempts (in progress)
        $activeAttempts = $attempts->where('status', ExamAttempt::STATUS_IN_PROGRESS);
        
        // Submitted count
        $submittedCount = $attempts->whereIn('status', [ExamAttempt::STATUS_SUBMITTED, ExamAttempt::STATUS_GRADED])->count();
        
        // Total violations
        $totalViolations = $attempts->sum('violation_count');
        
        // Recent violations
        $recentViolations = \App\Models\ProctoringLog::whereIn('attempt_id', $attempts->pluck('id'))
            ->with('attempt.user')
            ->latest()
            ->take(10)
            ->get();

        $statistics = $this->examService->getExamStatistics($exam);

        $statusCounts = [
            'not_started' => $attempts->where('status', ExamAttempt::STATUS_NOT_STARTED)->count(),
            'in_progress' => $attempts->where('status', ExamAttempt::STATUS_IN_PROGRESS)->count(),
            'submitted' => $attempts->whereIn('status', [ExamAttempt::STATUS_SUBMITTED, ExamAttempt::STATUS_GRADED])->count(),
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
     * Get real-time status of participants (for AJAX).
     */
    public function status(Exam $exam): \Illuminate\Http\JsonResponse
    {
        $this->authorize('monitor', $exam);

        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->with('user:id,name,student_id')
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'user_id' => $attempt->user_id,
                    'user_name' => $attempt->user->name,
                    'student_id' => $attempt->user->student_id,
                    'status' => $attempt->status,
                    'violation_count' => $attempt->violation_count,
                    'camera_enabled' => $attempt->camera_enabled,
                    'remaining_time' => $attempt->remaining_time,
                    'started_at' => $attempt->started_at?->format('H:i:s'),
                ];
            });

        return response()->json($attempts);
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

        $validated = $request->validate([
            'log_ids' => ['required', 'array'],
            'log_ids.*' => ['integer', 'exists:proctoring_logs,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->proctoringService->markAsReviewed(
            $validated['log_ids'],
            auth()->id(),
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

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*.id' => ['required', 'integer', 'exists:answers,id'],
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
}
