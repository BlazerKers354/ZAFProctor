<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamSetting;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Services\ExamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

        $query = Exam::where('created_by', $user->id)
            ->with(['course', 'settings'])
            ->withCount(['questions', 'attempts']);

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $exams = $query->latest()->paginate(15);
        $courses = $user->taughtCourses;

        return view('teacher.exams.index', compact('exams', 'courses'));
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create(): View
    {
        $courses = auth()->user()->taughtCourses;
        return view('teacher.exams.create', compact('courses'));
    }

    /**
     * Store a newly created exam.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'start_time' => ['required', 'date', 'after:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'duration' => ['required', 'integer', 'min:5', 'max:480'],
            'access_token' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:draft,published'],
        ]);

        $exam = null;
        
        DB::transaction(function () use ($validated, $request, &$exam) {
            // Create exam
            $exam = Exam::create([
                'course_id' => $validated['course_id'],
                'title' => $validated['title'],
                'description' => $validated['instructions'],
                'type' => 'scheduled', // Default to scheduled
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'duration' => $validated['duration'],
                'status' => $validated['status'] ?? 'draft',
                'created_by' => auth()->id(),
                'access_token' => $validated['access_token'] ?? strtoupper(Str::random(8)),
            ]);

            // Create exam settings
            ExamSetting::create([
                'exam_id' => $exam->id,
                // Proctoring settings
                'webcam_enabled' => $request->boolean('require_camera', true),
                'screen_capture_enabled' => true,
                'browser_lock_enabled' => $request->boolean('require_fullscreen', true),
                'tab_switch_detection' => true,
                'max_tab_switches' => $request->input('max_violations', 5),
                
                // Display settings
                'shuffle_questions' => $request->boolean('shuffle_questions', false),
                'shuffle_options' => false,
                'show_correct_answers' => false,
                'show_score' => true,
                
                // Attempt settings
                'max_attempts' => null,
                'grade_method' => 'highest',
                
                // Passing score
                'passing_score' => $request->input('passing_score', 60),
            ]);
        });

        return redirect()->route('teacher.questions.index', $exam)
            ->with('success', 'Ujian berhasil dibuat. Silakan tambahkan soal.');
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

        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:scheduled,flexible'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'duration' => ['required', 'integer', 'min:5', 'max:480'],
            'status' => ['required', 'in:draft,published,ongoing,completed'],
        ]);

        DB::transaction(function () use ($validated, $request, $exam) {
            // Update exam basic info
            $exam->update([
                'course_id' => $validated['course_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'type' => $validated['type'],
                'start_time' => $validated['type'] === 'scheduled' ? $validated['start_time'] : null,
                'end_time' => $validated['type'] === 'scheduled' ? $validated['end_time'] : null,
                'duration' => $validated['duration'],
                'status' => $validated['status'],
            ]);

            // Update or create exam settings
            $exam->settings()->updateOrCreate(
                ['exam_id' => $exam->id],
                [
                    // Proctoring settings
                    'webcam_enabled' => $request->boolean('webcam_enabled'),
                    'screen_capture_enabled' => $request->boolean('screen_capture_enabled'),
                    'browser_lock_enabled' => $request->boolean('browser_lock_enabled'),
                    'tab_switch_detection' => $request->boolean('tab_switch_detection'),
                    'max_tab_switches' => $request->input('max_tab_switches', 3),
                    
                    // Display settings
                    'shuffle_questions' => $request->boolean('shuffle_questions'),
                    'shuffle_options' => $request->boolean('shuffle_options'),
                    'show_correct_answers' => $request->boolean('show_correct_answers'),
                    'show_score' => $request->boolean('show_score'),
                    
                    // Attempt settings
                    'max_attempts' => $request->input('max_attempts') ?: null,
                    'grade_method' => $request->input('grade_method', 'highest'),
                ]
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

        $exam->delete();

        return redirect()->route('teacher.exams.index')
            ->with('success', 'Ujian berhasil dihapus.');
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
     * Update proctoring settings.
     */
    public function updateSettings(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorize('update', $exam);

        $validated = $request->validate([
            'snapshot_interval' => ['integer', 'min:10', 'max:120'],
            'detect_face' => ['boolean'],
            'detect_multiple_faces' => ['boolean'],
            'detect_tab_switch' => ['boolean'],
            'detect_fullscreen_exit' => ['boolean'],
            'detect_copy_paste' => ['boolean'],
            'detect_right_click' => ['boolean'],
            'block_keyboard_shortcuts' => ['boolean'],
            'warning_threshold' => ['integer', 'min:1', 'max:10'],
            'auto_submit_threshold' => ['integer', 'min:1', 'max:20'],
        ]);

        // Convert checkboxes to boolean
        foreach (['detect_face', 'detect_multiple_faces', 'detect_tab_switch', 'detect_fullscreen_exit', 'detect_copy_paste', 'detect_right_click', 'block_keyboard_shortcuts'] as $field) {
            $validated[$field] = $request->boolean($field);
        }

        $exam->settings()->updateOrCreate(
            ['exam_id' => $exam->id],
            $validated
        );

        return back()->with('success', 'Pengaturan proctoring berhasil diperbarui.');
    }
}
