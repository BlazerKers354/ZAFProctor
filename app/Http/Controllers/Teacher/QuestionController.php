<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions for an exam.
     */
    public function index(Exam $exam): View
    {
        $this->authorize('manageQuestions', $exam);

        $questions = $exam->questions()->with('options')->orderBy('order')->get();

        return view('teacher.questions.index', compact('exam', 'questions'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create(Exam $exam): View
    {
        $this->authorize('manageQuestions', $exam);

        return view('teacher.questions.create', compact('exam'));
    }

    /**
     * Store a newly created question.
     */
    public function store(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorize('manageQuestions', $exam);

        $validated = $request->validate([
            'type' => ['required', 'in:multiple_choice,essay'],
            'question' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1'],
            'explanation' => ['nullable', 'string'],
            'options' => ['required_if:type,multiple_choice', 'array', 'min:2'],
            'options.*.text' => ['required_if:type,multiple_choice', 'string'],
            'correct_option' => ['required_if:type,multiple_choice', 'integer'],
        ]);

        DB::transaction(function () use ($validated, $exam) {
            $question = Question::create([
                'exam_id' => $exam->id,
                'type' => $validated['type'],
                'question' => $validated['question'],
                'points' => $validated['points'],
                'explanation' => $validated['explanation'] ?? null,
                'order' => $exam->questions()->max('order') + 1,
            ]);

            // Create options for multiple choice
            if ($validated['type'] === Question::TYPE_MULTIPLE_CHOICE && isset($validated['options'])) {
                $labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                
                foreach ($validated['options'] as $index => $option) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_label' => $labels[$index] ?? chr(65 + $index),
                        'option_text' => $option['text'],
                        'is_correct' => $index == $validated['correct_option'],
                        'order' => $index,
                    ]);
                }
            }
        });

        return redirect()->route('teacher.exams.questions.index', $exam)
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(Exam $exam, Question $question): View
    {
        $this->authorize('manageQuestions', $exam);

        $question->load('options');

        return view('teacher.questions.edit', compact('exam', 'question'));
    }

    /**
     * Update the specified question.
     */
    public function update(Request $request, Exam $exam, Question $question): RedirectResponse
    {
        $this->authorize('manageQuestions', $exam);

        $validated = $request->validate([
            'question' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1'],
            'explanation' => ['nullable', 'string'],
            'options' => ['required_if:type,multiple_choice', 'array', 'min:2'],
            'options.*.text' => ['required_if:type,multiple_choice', 'string'],
            'correct_option' => ['required_if:type,multiple_choice', 'integer'],
        ]);

        DB::transaction(function () use ($validated, $question) {
            $question->update([
                'question' => $validated['question'],
                'points' => $validated['points'],
                'explanation' => $validated['explanation'] ?? null,
            ]);

            // Update options for multiple choice
            if ($question->isMultipleChoice() && isset($validated['options'])) {
                $question->options()->delete();
                
                $labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                
                foreach ($validated['options'] as $index => $option) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_label' => $labels[$index] ?? chr(65 + $index),
                        'option_text' => $option['text'],
                        'is_correct' => $index == $validated['correct_option'],
                        'order' => $index,
                    ]);
                }
            }
        });

        return redirect()->route('teacher.exams.questions.index', $exam)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    /**
     * Remove the specified question.
     */
    public function destroy(Exam $exam, Question $question): RedirectResponse
    {
        $this->authorize('manageQuestions', $exam);

        $question->delete();

        // Reorder remaining questions
        $exam->questions()->orderBy('order')->get()->each(function ($q, $index) {
            $q->update(['order' => $index + 1]);
        });

        return redirect()->route('teacher.exams.questions.index', $exam)
            ->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Reorder questions.
     */
    public function reorder(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorize('manageQuestions', $exam);

        $validated = $request->validate([
            'questions' => ['required', 'array'],
            'questions.*' => ['integer', 'exists:questions,id'],
        ]);

        foreach ($validated['questions'] as $index => $questionId) {
            Question::where('id', $questionId)->update(['order' => $index + 1]);
        }

        return back()->with('success', 'Urutan soal berhasil diperbarui.');
    }
}
