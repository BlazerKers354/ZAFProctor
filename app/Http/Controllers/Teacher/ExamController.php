<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamSetting;
use App\Services\ExamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function __construct(
        protected ExamService $examService
    ) {}

    /**
     * Display a listing of exams.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $pendingGradingOnly = $request->boolean('pending_grading');
        $fromNotification = $request->boolean('from_notification');

        // Show exams the teacher created OR exams in courses they teach
        $teacherCourseIds = $user->taughtCourses()->pluck('id');

        $query = Exam::where(function ($q) use ($user, $teacherCourseIds) {
                $q->where('created_by', $user->id)
                  ->orWhereIn('course_id', $teacherCourseIds);
            })
            ->with(['course', 'settings'])
            ->withCount([
                'questions',
                'attempts',
                'attempts as pending_grading_attempts_count' => function ($q) {
                    $q->where('status', ExamAttempt::STATUS_SUBMITTED);
                },
            ]);

        // Search by title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        // Filter by course
        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($pendingGradingOnly) {
            $query->whereHas('attempts', function ($q) {
                $q->where('status', ExamAttempt::STATUS_SUBMITTED);
            });
        }

        $exams = $query->latest()->paginate(15)->withQueryString();
        $courses = $user->taughtCourses;

        return view('teacher.exams.index', compact('exams', 'courses', 'pendingGradingOnly', 'fromNotification'));
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create(): View
    {
        $this->authorize('create', Exam::class);

        $courses = auth()->user()->taughtCourses;
        return view('teacher.exams.create', compact('courses'));
    }

    /**
     * Store a newly created exam.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Exam::class);

        $isScheduled = $request->input('type', 'scheduled') === 'scheduled';

        $rules = [
            'course_id' => [
                'required',
                'exists:courses,id',
                Rule::in(auth()->user()->taughtCourses()->pluck('id')->toArray()),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:scheduled,flexible'],
            'duration' => ['required', 'integer', 'min:5', 'max:480'],
            'access_token' => ['nullable', 'string', 'max:32', Rule::unique('exams', 'access_token')],
            'status' => ['nullable', 'in:draft,published'],
            // Settings validation
            'max_attempts' => ['nullable', 'integer', 'min:0', 'max:10'],
            'max_tab_switches' => ['nullable', 'integer', 'min:0', 'max:20'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'grade_method' => ['nullable', 'in:highest,latest,average'],
        ];

        if ($isScheduled) {
            $rules['start_time'] = ['required', 'date', 'after_or_equal:now'];
            $rules['end_time'] = ['required', 'date', 'after:start_time'];
        } else {
            $rules['start_time'] = ['nullable', 'date'];
            $rules['end_time'] = ['nullable', 'date'];
        }

        $validated = $request->validate($rules);

        // Validate duration vs time window for scheduled exams
        if ($isScheduled && $request->filled('start_time') && $request->filled('end_time')) {
            $startTime = \Carbon\Carbon::parse($validated['start_time']);
            $endTime = \Carbon\Carbon::parse($validated['end_time']);
            $timeWindowMinutes = $startTime->diffInMinutes($endTime);

            if ($validated['duration'] > $timeWindowMinutes) {
                return back()->withErrors([
                    'duration' => "Durasi ujian ({$validated['duration']} menit) tidak boleh melebihi jendela waktu yang tersedia ({$timeWindowMinutes} menit)."
                ])->withInput();
            }
        }

        $requestedStatus = $validated['status'] ?? Exam::STATUS_DRAFT;
        $finalStatus = $requestedStatus === Exam::STATUS_PUBLISHED
            ? Exam::STATUS_DRAFT
            : $requestedStatus;

        $exam = null;
        $maxViolationsInput = $this->resolveMaxViolationsInput($request);
        
        DB::transaction(function () use ($validated, $request, &$exam, $isScheduled, $maxViolationsInput, $finalStatus) {
            // Create exam
            $exam = Exam::create([
                'course_id' => $validated['course_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'start_time' => $isScheduled ? $validated['start_time'] : null,
                'end_time' => $isScheduled ? $validated['end_time'] : null,
                'duration' => $validated['duration'],
                'status' => $finalStatus,
                'created_by' => auth()->id(),
                'access_token' => $validated['access_token'] ?? Exam::generateAccessToken(),
            ]);

            // Create exam settings
            ExamSetting::create([
                'exam_id' => $exam->id,
            ] + $this->buildExamSettingsPayload($request, $maxViolationsInput, true));
        });

        $redirect = redirect()->route('teacher.questions.index', $exam)
            ->with('success', 'Ujian berhasil dibuat. Silakan tambahkan soal.');

        if ($requestedStatus === Exam::STATUS_PUBLISHED) {
            $redirect->with('warning', 'Ujian otomatis disimpan sebagai draft. Tambahkan minimal 1 soal sebelum dipublikasikan.');
        }

        return $redirect;
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam): View
    {
        $this->authorize('view', $exam);

        $exam->load(['course', 'questions.options', 'settings']);
        $statistics = $this->examService->getExamStatistics($exam);

        return view('teacher.exams.show', compact('exam', 'statistics'));
    }

    /**
     * Show the form for editing the specified exam.
     */
    public function edit(Exam $exam): View
    {
        $this->authorize('update', $exam);

        $courses = auth()->user()->taughtCourses;
        $exam->load('settings');

        return view('teacher.exams.edit', compact('exam', 'courses'));
    }

    /**
     * Update the specified exam.
     */
    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorize('update', $exam);

        $isScheduled = $request->input('type', 'scheduled') === 'scheduled';

        $rules = [
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:scheduled,flexible'],
            'duration' => ['required', 'integer', 'min:5', 'max:480'],
            'status' => ['required', 'in:draft,published,ongoing,completed'],
            // Settings validation
            'max_attempts' => ['nullable', 'integer', 'min:0', 'max:10'],
            'max_tab_switches' => ['nullable', 'integer', 'min:0', 'max:20'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'grade_method' => ['nullable', 'in:highest,latest,average'],
        ];

        if ($isScheduled) {
            $rules['start_time'] = ['required', 'date'];
            $rules['end_time'] = ['required', 'date', 'after:start_time'];
        } else {
            $rules['start_time'] = ['nullable', 'date'];
            $rules['end_time'] = ['nullable', 'date'];
        }

        $validated = $request->validate($rules);

        // Validate duration vs time window for scheduled exams
        if ($isScheduled && $request->filled('start_time') && $request->filled('end_time')) {
            $startTime = \Carbon\Carbon::parse($validated['start_time']);
            $endTime = \Carbon\Carbon::parse($validated['end_time']);
            $timeWindowMinutes = $startTime->diffInMinutes($endTime);

            if ($validated['duration'] > $timeWindowMinutes) {
                return back()->withErrors([
                    'duration' => "Durasi ujian ({$validated['duration']} menit) tidak boleh melebihi jendela waktu yang tersedia ({$timeWindowMinutes} menit)."
                ])->withInput();
            }
        }

        $requestedStatus = $validated['status'];
        if ($requestedStatus === Exam::STATUS_PUBLISHED && !$exam->questions()->exists()) {
            return back()->withErrors([
                'status' => 'Ujian tidak dapat dipublikasikan karena belum memiliki soal. Tambahkan minimal 1 soal terlebih dahulu.',
            ])->withInput();
        }

        $maxViolationsInput = $this->resolveMaxViolationsInput($request);

        DB::transaction(function () use ($validated, $request, $exam, $isScheduled, $maxViolationsInput, $requestedStatus) {
            // Update exam basic info
            $exam->update([
                'course_id' => $validated['course_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'start_time' => $isScheduled ? $validated['start_time'] : null,
                'end_time' => $isScheduled ? $validated['end_time'] : null,
                'duration' => $validated['duration'],
                'status' => $requestedStatus,
            ]);

            // Update or create exam settings
            $exam->settings()->updateOrCreate(
                ['exam_id' => $exam->id],
                $this->buildExamSettingsPayload($request, $maxViolationsInput, false)
            );
        });

        return redirect()->route('teacher.exams.show', $exam)
            ->with('success', 'Ujian berhasil diperbarui.');
    }

    /**
     * Remove the specified exam.
     */
    public function destroy(Exam $exam): RedirectResponse
    {
        $this->authorize('delete', $exam);

        try {
            DB::transaction(function () use ($exam) {
                $exam->delete();
            });

            return redirect()->route('teacher.exams.index')
                ->with('success', 'Ujian berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Failed to delete exam: ' . $e->getMessage(), ['exam_id' => $exam->id]);
            
            return back()->with('error', 'Gagal menghapus ujian. Silakan coba lagi.');
        }
    }

    /**
     * Publish the exam.
     */
    public function publish(Exam $exam): RedirectResponse
    {
        $this->authorize('update', $exam);

        if ($exam->questions()->count() === 0) {
            return back()->with('error', 'Tidak dapat mempublikasikan ujian tanpa soal.');
        }

        $exam->update(['status' => Exam::STATUS_PUBLISHED]);

        return back()->with('success', 'Ujian berhasil dipublikasikan.');
    }

    /**
     * Regenerate access token.
     */
    public function regenerateToken(Exam $exam): RedirectResponse
    {
        $this->authorize('update', $exam);

        $exam->regenerateToken();

        return back()->with('success', 'Token akses berhasil diperbarui.');
    }

    /**
     * Duplicate an exam.
     */
    public function duplicate(Exam $exam): RedirectResponse
    {
        $this->authorize('view', $exam);

        $newExam = null;

        DB::transaction(function () use ($exam, &$newExam) {
            // Duplicate exam
            $newExam = $exam->replicate();
            $newExam->title = $exam->title . ' (Salinan)';
            $newExam->status = Exam::STATUS_DRAFT;
            $newExam->access_token = Exam::generateAccessToken();
            $newExam->created_by = auth()->id();
            $newExam->save();

            // Duplicate settings
            if ($exam->settings) {
                $newSettings = $exam->settings->replicate();
                $newSettings->exam_id = $newExam->id;
                $newSettings->save();
            }

            // Duplicate questions and options
            foreach ($exam->questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->exam_id = $newExam->id;
                $newQuestion->save();

                foreach ($question->options as $option) {
                    $newOption = $option->replicate();
                    $newOption->question_id = $newQuestion->id;
                    $newOption->save();
                }
            }
        });

        return redirect()->route('teacher.exams.show', $newExam)
            ->with('success', 'Ujian berhasil diduplikasi.');
    }

    /**
     * Show exam results.
     */
    public function results(Exam $exam): View
    {
        $this->authorize('view', $exam);

        $exam->load(['course', 'questions', 'settings']);
        
        $attempts = $exam->attempts()
            ->with(['student', 'answers'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $statistics = $this->examService->getExamStatistics($exam);

        return view('teacher.exams.results', compact('exam', 'attempts', 'statistics'));
    }

    /**
     * Export exam data.
     */
    public function export(Exam $exam)
    {
        try {
            $this->authorize('view', $exam);

            $exam->load(['course', 'questions.options', 'attempts.student.class', 'attempts.answers']);
            
            $data = [];
            
            foreach ($exam->attempts as $attempt) {
                $row = [
                    'Nama Siswa' => $attempt->student->name ?? '-',
                    'NIS' => $attempt->student->student_id ?? '-',
                    'Kelas' => $attempt->student->class?->name ?? '-',
                    'Waktu Mulai' => $attempt->started_at?->format('d/m/Y H:i:s') ?? '-',
                    'Waktu Selesai' => $attempt->finished_at?->format('d/m/Y H:i:s') ?? '-',
                    'Durasi (menit)' => $attempt->started_at && $attempt->finished_at 
                        ? round($attempt->started_at->diffInMinutes($attempt->finished_at), 1) 
                        : '-',
                    'Nilai' => $attempt->score ?? '-',
                    'Status' => ucfirst($attempt->status ?? 'unknown'),
                    'Total Pelanggaran' => $attempt->violation_count ?? 0,
                ];
                
                $data[] = $row;
            }
            
            // Generate CSV
            $filename = 'hasil_' . Str::slug($exam->title) . '_' . now()->format('YmdHis') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function () use ($data) {
                try {
                    $file = fopen('php://output', 'w');
                    
                    if (!$file) {
                        throw new \Exception('Failed to open output stream');
                    }
                    
                    // Add BOM for UTF-8
                    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                    
                    // Headers
                    if (!empty($data)) {
                        fputcsv($file, array_keys($data[0]));
                    }
                    
                    // Data rows
                    foreach ($data as $row) {
                        fputcsv($file, $row);
                    }
                    
                    fclose($file);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('CSV export stream error: ' . $e->getMessage());
                }
            };
            
            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Exam export failed: ' . $e->getMessage(), [
                'exam_id' => $exam->id,
            ]);
            
            return back()->with('error', 'Gagal mengekspor hasil ujian. Silakan coba lagi.');
        }
    }

    /**
     * Show grade form for an attempt.
     */
    public function gradeForm(ExamAttempt $attempt): View
    {
        $exam = $attempt->exam;
        $this->authorize('view', $exam);

        $attempt->load(['student', 'answers.question.options', 'exam.questions.options']);

        return view('teacher.exams.grade', compact('exam', 'attempt'));
    }

    /**
     * Submit grade for an attempt.
     */
    public function submitGrade(Request $request, ExamAttempt $attempt): RedirectResponse
    {
        try {
            $exam = $attempt->exam;
            $this->authorize('update', $exam);

            $validated = $request->validate([
                'scores' => ['required', 'array'],
                'scores.*' => ['required', 'numeric', 'min:0'],
                'feedback' => ['nullable', 'string', 'max:1000'],
            ]);

            DB::transaction(function () use ($validated, $attempt) {
                foreach ($validated['scores'] as $answerId => $score) {
                    $answer = $attempt->answers()->find($answerId);
                    if ($answer) {
                        // Get max points for this question
                        $maxPoints = $answer->question->points ?? 1;
                        $earnedPoints = min($score, $maxPoints);
                        
                        // Update answer with correct field (points_earned)
                        $answer->update([
                            'points_earned' => $earnedPoints,
                            'is_correct' => $earnedPoints >= ($maxPoints * 0.5), // 50% threshold
                        ]);
                    }
                }

                // Recalculate attempt score using the model method
                $attempt->calculateScore();
                
                // Update attempt status and feedback
                $attempt->update([
                    'status' => ExamAttempt::STATUS_GRADED,
                    'feedback' => $validated['feedback'] ?? null,
                ]);
            });

            return redirect()->route('teacher.exams.results', $exam)
                ->with('success', 'Nilai berhasil disimpan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to submit grade: ' . $e->getMessage(), [
                'attempt_id' => $attempt->id,
            ]);
            
            return back()->with('error', 'Gagal menyimpan nilai. Silakan coba lagi.');
        }
    }

    /**
     * Update proctoring settings.
     */
    public function updateSettings(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorize('update', $exam);

        $validated = $request->validate([
            'snapshot_interval' => ['integer', 'min:10', 'max:120'],
            'webcam_enabled' => ['boolean'],
            'browser_lock_enabled' => ['boolean'],
            'tab_switch_detection' => ['boolean'],
            'block_keyboard_shortcuts' => ['boolean'],
            'auto_submit_threshold' => ['integer', 'min:0', 'max:20'],
        ]);

        // Convert checkboxes to boolean
        foreach (['webcam_enabled', 'browser_lock_enabled', 'tab_switch_detection', 'block_keyboard_shortcuts'] as $field) {
            $validated[$field] = $request->boolean($field);
        }

        // Keep both legacy and new threshold fields synchronized.
        if (array_key_exists('auto_submit_threshold', $validated)) {
            $validated['max_tab_switches'] = $validated['auto_submit_threshold'];
        }

        $exam->settings()->updateOrCreate(
            ['exam_id' => $exam->id],
            $validated
        );

        return back()->with('success', 'Pengaturan proctoring berhasil diperbarui.');
    }

    protected function resolveMaxViolationsInput(Request $request): int
    {
        return $request->filled('max_tab_switches')
            ? (int) $request->input('max_tab_switches')
            : 5;
    }

    protected function buildExamSettingsPayload(Request $request, int $maxViolationsInput, bool $creating): array
    {
        return [
            'webcam_enabled' => $request->boolean('webcam_enabled', $creating),
            'browser_lock_enabled' => $request->boolean('browser_lock_enabled', $creating),
            'tab_switch_detection' => $request->boolean('tab_switch_detection', $creating),
            'max_tab_switches' => $maxViolationsInput,
            'auto_submit_threshold' => $maxViolationsInput,
            'block_keyboard_shortcuts' => $request->boolean('block_keyboard_shortcuts', $creating),
            'shuffle_questions' => $request->boolean('shuffle_questions', false),
            'shuffle_options' => $request->boolean('shuffle_options', false),
            'show_correct_answers' => $request->boolean('show_correct_answers', false),
            'show_score' => $request->boolean('show_score', $creating),
            'max_attempts' => $request->filled('max_attempts') ? (int) $request->input('max_attempts') : 1,
            'grade_method' => $request->input('grade_method', 'highest'),
            'passing_score' => $request->input('passing_score', 60),
        ];
    }
}
