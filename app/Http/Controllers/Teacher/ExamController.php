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
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'start_time' => ['required', 'date', 'after:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'shuffle_questions' => ['boolean'],
            'shuffle_answers' => ['boolean'],
            'show_result' => ['boolean'],
            'require_camera' => ['boolean'],
            'require_fullscreen' => ['boolean'],
            'max_violations' => ['integer', 'min:1', 'max:20'],
            'passing_score' => ['integer', 'min:0', 'max:100'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['access_token'] = Str::random(32);
        $validated['status'] = Exam::STATUS_DRAFT;
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions', true);
        $validated['shuffle_answers'] = $request->boolean('shuffle_answers', true);
        $validated['show_result'] = $request->boolean('show_result', false);
        $validated['require_camera'] = $request->boolean('require_camera', true);
        $validated['require_fullscreen'] = $request->boolean('require_fullscreen', true);

        $exam = Exam::create($validated);

        // Create default settings
        ExamSetting::create(array_merge(
            ['exam_id' => $exam->id],
            ExamSetting::getDefaults()
        ));

        return redirect()->route('teacher.exams.questions.index', $exam)
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
            'instructions' => ['nullable', 'string'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'shuffle_questions' => ['boolean'],
            'shuffle_answers' => ['boolean'],
            'show_result' => ['boolean'],
            'require_camera' => ['boolean'],
            'require_fullscreen' => ['boolean'],
            'max_violations' => ['integer', 'min:1', 'max:20'],
            'passing_score' => ['integer', 'min:0', 'max:100'],
        ]);

        $validated['shuffle_questions'] = $request->boolean('shuffle_questions', true);
        $validated['shuffle_answers'] = $request->boolean('shuffle_answers', true);
        $validated['show_result'] = $request->boolean('show_result', false);
        $validated['require_camera'] = $request->boolean('require_camera', true);
        $validated['require_fullscreen'] = $request->boolean('require_fullscreen', true);

        $exam->update($validated);

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
