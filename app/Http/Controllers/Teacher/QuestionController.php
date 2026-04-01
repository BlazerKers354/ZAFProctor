<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
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

        // Custom validation rules based on question type
        $rules = [
            'question_type' => ['required', 'in:multiple_choice,essay'],
            'question' => ['required', 'string', 'min:10'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'explanation' => ['nullable', 'string'],
            'question_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        // Add multiple choice specific validation
        if ($request->input('question_type') === 'multiple_choice') {
            $rules['options'] = ['required', 'array', 'min:2', 'max:8'];
            $rules['options.*.text'] = ['required', 'string', 'min:1'];
            $rules['correct_option'] = ['required', 'integer', 'min:0'];
        }

        $messages = [
            'question.required' => 'Pertanyaan wajib diisi.',
            'question.min' => 'Pertanyaan minimal 10 karakter.',
            'points.required' => 'Poin wajib diisi.',
            'points.min' => 'Poin minimal 1.',
            'points.max' => 'Poin maksimal 100.',
            'options.required' => 'Pilihan jawaban wajib diisi untuk soal pilihan ganda.',
            'options.min' => 'Minimal 2 pilihan jawaban.',
            'options.*.text.required' => 'Semua pilihan jawaban harus diisi.',
            'options.*.text.min' => 'Pilihan jawaban tidak boleh kosong.',
            'correct_option.required' => 'Pilih salah satu jawaban yang benar.',
            'question_image.image' => 'File harus berupa gambar.',
            'question_image.max' => 'Ukuran gambar maksimal 2MB.',
        ];

        $validated = $request->validate($rules, $messages);

        // Filter empty options
        if (isset($validated['options'])) {
            $validated['options'] = array_filter($validated['options'], function ($option) {
                return !empty(trim($option['text'] ?? ''));
            });
            $validated['options'] = array_values($validated['options']); // Re-index
            
            // Validate minimum 2 options after filtering
            if (count($validated['options']) < 2) {
                return back()->withInput()->withErrors(['options' => 'Minimal 2 pilihan jawaban yang valid.']);
            }

            // Validate correct_option is within range
            if ($validated['correct_option'] >= count($validated['options'])) {
                return back()->withInput()->withErrors(['correct_option' => 'Jawaban benar tidak valid.']);
            }
        }

        DB::transaction(function () use ($validated, $exam, $request) {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('question_image')) {
                $imagePath = $request->file('question_image')->store('questions', 'public');
            }

            $question = Question::create([
                'exam_id' => $exam->id,
                'type' => $validated['question_type'],
                'question' => $validated['question'],
                'question_image' => $imagePath,
                'points' => $validated['points'],
                'explanation' => $validated['explanation'] ?? null,
                'order' => $exam->questions()->max('order') + 1,
            ]);

            // Create options for multiple choice
            if ($validated['question_type'] === Question::TYPE_MULTIPLE_CHOICE && isset($validated['options'])) {
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

        // Handle different save actions
        if ($request->input('action') === 'save_and_new') {
            return redirect()->route('teacher.questions.create', $exam)
                ->with('success', 'Soal berhasil ditambahkan. Silakan tambah soal berikutnya.');
        }

        return redirect()->route('teacher.questions.index', $exam)
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

        $isMultipleChoice = $question->type === Question::TYPE_MULTIPLE_CHOICE;
        
        // Build validation rules
        $rules = [
            'question' => ['required', 'string', 'min:10'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'explanation' => ['nullable', 'string'],
            'question_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ];

        if ($isMultipleChoice) {
            $rules['options'] = ['required', 'array', 'min:2', 'max:8'];
            $rules['options.*.text'] = ['required', 'string', 'min:1'];
            $rules['correct_option'] = ['required', 'integer', 'min:0'];
        }

        $messages = [
            'question.required' => 'Pertanyaan wajib diisi.',
            'question.min' => 'Pertanyaan minimal 10 karakter.',
            'points.required' => 'Poin wajib diisi.',
            'points.min' => 'Poin minimal 1.',
            'points.max' => 'Poin maksimal 100.',
            'options.required' => 'Pilihan jawaban wajib diisi.',
            'options.min' => 'Minimal 2 pilihan jawaban.',
            'options.*.text.required' => 'Semua pilihan jawaban harus diisi.',
            'options.*.text.min' => 'Pilihan jawaban tidak boleh kosong.',
            'correct_option.required' => 'Pilih salah satu jawaban yang benar.',
            'question_image.image' => 'File harus berupa gambar.',
            'question_image.max' => 'Ukuran gambar maksimal 2MB.',
        ];

        $validated = $request->validate($rules, $messages);

        // Filter empty options for multiple choice
        if ($isMultipleChoice && isset($validated['options'])) {
            $validated['options'] = array_filter($validated['options'], function ($option) {
                return !empty(trim($option['text'] ?? ''));
            });
            $validated['options'] = array_values($validated['options']);
            
            if (count($validated['options']) < 2) {
                return back()->withInput()->withErrors(['options' => 'Minimal 2 pilihan jawaban yang valid.']);
            }

            if ($validated['correct_option'] >= count($validated['options'])) {
                return back()->withInput()->withErrors(['correct_option' => 'Jawaban benar tidak valid.']);
            }
        }

        DB::transaction(function () use ($validated, $question, $request) {
            // Handle image upload/removal
            $imagePath = $question->question_image;
            
            if ($request->has('remove_image') && $request->remove_image) {
                // Remove existing image
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = null;
            } elseif ($request->hasFile('question_image')) {
                // Remove old image if exists
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('question_image')->store('questions', 'public');
            }

            $question->update([
                'question' => $validated['question'],
                'question_image' => $imagePath,
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

        return redirect()->route('teacher.questions.index', $exam)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    /**
     * Remove the specified question.
     */
    public function destroy(Request $request, Exam $exam, Question $question): RedirectResponse|JsonResponse
    {
        $this->authorize('manageQuestions', $exam);

        try {
            // Delete associated image if exists
            if ($question->question_image) {
                try {
                    Storage::disk('public')->delete($question->question_image);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to delete question image: ' . $e->getMessage());
                }
            }

            $question->delete();

            // Reorder remaining questions
            $exam->questions()->orderBy('order')->get()->each(function ($q, $index) {
                $q->update(['order' => $index + 1]);
            });

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Soal berhasil dihapus.']);
            }

            return redirect()->route('teacher.questions.index', $exam)
                ->with('success', 'Soal berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to delete question: ' . $e->getMessage(), [
                'question_id' => $question->id,
                'exam_id' => $exam->id,
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus soal.'], 500);
            }
            
            return redirect()->route('teacher.questions.index', $exam)
                ->with('error', 'Gagal menghapus soal.');
        }
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

    /**
     * Get question detail for modal view.
     */
    public function detail(Exam $exam, Question $question): JsonResponse
    {
        $this->authorize('manageQuestions', $exam);

        $question->load('options');

        return response()->json([
            'id' => $question->id,
            'type' => $question->type,
            'question' => $question->question,
            'points' => $question->points,
            'explanation' => $question->explanation,
            'options' => $question->options->map(function ($option) {
                return [
                    'option_label' => $option->option_label,
                    'option_text' => $option->option_text,
                    'is_correct' => $option->is_correct,
                ];
            }),
        ]);
    }

    /**
     * Duplicate a question.
     */
    public function duplicate(Request $request, Exam $exam): JsonResponse
    {
        $this->authorize('manageQuestions', $exam);

        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
        ]);

        $originalQuestion = Question::with('options')->findOrFail($validated['question_id']);

        DB::transaction(function () use ($originalQuestion, $exam) {
            $newQuestion = Question::create([
                'exam_id' => $exam->id,
                'type' => $originalQuestion->type,
                'question' => $originalQuestion->question . ' (Copy)',
                'points' => $originalQuestion->points,
                'explanation' => $originalQuestion->explanation,
                'order' => $exam->questions()->max('order') + 1,
            ]);

            if ($originalQuestion->type === Question::TYPE_MULTIPLE_CHOICE) {
                foreach ($originalQuestion->options as $option) {
                    QuestionOption::create([
                        'question_id' => $newQuestion->id,
                        'option_label' => $option->option_label,
                        'option_text' => $option->option_text,
                        'is_correct' => $option->is_correct,
                        'order' => $option->order,
                    ]);
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Soal berhasil diduplikat.']);
    }

    /**
     * Delete multiple questions.
     */
    public function deleteMultiple(Request $request, Exam $exam): JsonResponse
    {
        $this->authorize('manageQuestions', $exam);

        $validated = $request->validate([
            'question_ids' => ['required', 'array'],
            'question_ids.*' => ['integer', 'exists:questions,id'],
        ]);

        Question::whereIn('id', $validated['question_ids'])->delete();

        // Reorder remaining questions
        $exam->questions()->orderBy('order')->get()->each(function ($q, $index) {
            $q->update(['order' => $index + 1]);
        });

        return response()->json(['success' => true, 'message' => 'Soal berhasil dihapus.']);
    }

    /**
     * Download CSV template.
     */
    public function downloadTemplate(Exam $exam)
    {
        $this->authorize('manageQuestions', $exam);

        $headers = [
            'type',
            'question',
            'points',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'option_e',
            'correct_answer',
            'explanation'
        ];

        $examples = [
            [
                'multiple_choice',
                'Apa ibukota Indonesia?',
                '10',
                'Jakarta',
                'Bandung',
                'Surabaya',
                'Medan',
                '',
                'A',
                'Jakarta adalah ibukota Indonesia sejak tahun 1945'
            ],
            [
                'multiple_choice',
                'Berapa hasil dari 2 + 2?',
                '5',
                '3',
                '4',
                '5',
                '6',
                '',
                'B',
                ''
            ],
            [
                'essay',
                'Jelaskan pengertian Pancasila!',
                '20',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                'Pancasila adalah dasar negara Indonesia yang terdiri dari lima sila'
            ],
        ];

        $filename = 'template_import_soal_' . $exam->id . '.csv';
        $handle = fopen('php://temp', 'r+');
        
        // Add BOM for UTF-8
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Write headers
        fputcsv($handle, $headers);
        
        // Write examples
        foreach ($examples as $example) {
            fputcsv($handle, $example);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export questions to CSV.
     */
    public function export(Exam $exam)
    {
        $this->authorize('manageQuestions', $exam);

        $questions = $exam->questions()->with('options')->orderBy('order')->get();

        $headers = [
            'type',
            'question',
            'points',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'option_e',
            'correct_answer',
            'explanation'
        ];

        $filename = 'soal_' . str_replace(' ', '_', $exam->title) . '_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'r+');
        
        // Add BOM for UTF-8
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Write headers
        fputcsv($handle, $headers);
        
        // Write questions
        foreach ($questions as $question) {
            $row = [
                $question->type,
                $question->question,
                $question->points,
            ];

            if ($question->type === Question::TYPE_MULTIPLE_CHOICE) {
                $options = $question->options->sortBy('order')->values();
                $correctAnswer = '';
                
                for ($i = 0; $i < 5; $i++) {
                    if (isset($options[$i])) {
                        $row[] = $options[$i]->option_text;
                        if ($options[$i]->is_correct) {
                            $correctAnswer = $options[$i]->option_label;
                        }
                    } else {
                        $row[] = '';
                    }
                }
                
                $row[] = $correctAnswer;
            } else {
                $row = array_merge($row, ['-', '-', '-', '-', '-', '-']);
            }

            $row[] = $question->explanation ?? '';
            
            fputcsv($handle, $row);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Import questions from CSV.
     */
    public function import(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorize('manageQuestions', $exam);

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $handle = null;
        
        try {
            $file = $request->file('csv_file');
            
            if (!$file) {
                return redirect()->route('teacher.questions.index', $exam)
                    ->with('error', 'File tidak ditemukan.');
            }
            
            $filePath = $file->getRealPath();
            if (!$filePath || !file_exists($filePath)) {
                return redirect()->route('teacher.questions.index', $exam)
                    ->with('error', 'File tidak dapat diakses.');
            }
            
            $handle = @fopen($filePath, 'r');
            if ($handle === false) {
                \Illuminate\Support\Facades\Log::error('Failed to open CSV file: ' . $filePath);
                return redirect()->route('teacher.questions.index', $exam)
                    ->with('error', 'Tidak dapat membuka file CSV.');
            }
            
            // Skip BOM if present
            $bom = @fread($handle, 3);
            if ($bom === false) {
                throw new \Exception('Gagal membaca file');
            }
            
            if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                rewind($handle);
            }
            
            // Read header
            $header = @fgetcsv($handle);
            if ($header === false || empty($header)) {
                throw new \Exception('File CSV kosong atau format tidak valid');
            }
            
            $imported = 0;
            $errors = [];
            $lineNumber = 1;

            DB::beginTransaction();

            while (($row = @fgetcsv($handle)) !== false) {
                $lineNumber++;
                
                if (empty(array_filter($row))) {
                    continue; // Skip empty rows
                }

                try {
                    // Ensure row has same number of columns as header
                    $row = array_pad($row, count($header), null);
                    $data = @array_combine($header, $row);
                    
                    if ($data === false) {
                        $errors[] = "Baris $lineNumber: Format kolom tidak sesuai";
                        continue;
                    }
                    
                    if (!isset($data['type']) || !isset($data['question']) || !isset($data['points'])) {
                        $errors[] = "Baris $lineNumber: Data tidak lengkap";
                        continue;
                    }

                    $type = strtolower(trim($data['type'] ?? ''));
                    if (!in_array($type, ['multiple_choice', 'essay'])) {
                        $errors[] = "Baris $lineNumber: Tipe soal tidak valid";
                        continue;
                    }
                    
                    // Validate points
                    $points = filter_var($data['points'], FILTER_VALIDATE_INT);
                    if ($points === false || $points < 1 || $points > 100) {
                        $errors[] = "Baris $lineNumber: Poin harus antara 1-100";
                        continue;
                    }

                    $question = Question::create([
                        'exam_id' => $exam->id,
                        'type' => $type,
                        'question' => trim($data['question'] ?? ''),
                        'points' => $points,
                        'explanation' => !empty($data['explanation']) && $data['explanation'] !== '-' ? trim($data['explanation']) : null,
                        'order' => $exam->questions()->max('order') + 1 + $imported,
                    ]);
                    
                    if (!$question) {
                        throw new \Exception('Gagal membuat soal');
                    }

                    if ($type === 'multiple_choice') {
                        $labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                        $correctAnswer = strtoupper(trim($data['correct_answer'] ?? ''));
                        $optionCount = 0;

                        foreach (['option_a', 'option_b', 'option_c', 'option_d', 'option_e'] as $index => $optionKey) {
                            if (isset($data[$optionKey]) && !empty(trim($data[$optionKey])) && trim($data[$optionKey]) !== '-') {
                                QuestionOption::create([
                                    'question_id' => $question->id,
                                    'option_label' => $labels[$index],
                                    'option_text' => trim($data[$optionKey]),
                                    'is_correct' => $labels[$index] === $correctAnswer,
                                    'order' => $index,
                                ]);
                                $optionCount++;
                            }
                        }
                        
                        // Validate minimum options
                        if ($optionCount < 2) {
                            $errors[] = "Baris $lineNumber: Minimal 2 pilihan jawaban";
                            $question->delete();
                            continue;
                        }
                    }

                    $imported++;

                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Import error at line $lineNumber: " . $e->getMessage());
                    $errors[] = "Baris $lineNumber: Format tidak valid";
                }
            }

            if ($imported > 0) {
                DB::commit();
                $message = "$imported soal berhasil diimport.";
                if (count($errors) > 0) {
                    $message .= " " . count($errors) . " baris tidak dapat diproses.";
                }
                return redirect()->route('teacher.questions.index', $exam)
                    ->with('success', $message)
                    ->with('import_errors', $errors);
            } else {
                DB::rollBack();
                return redirect()->route('teacher.questions.index', $exam)
                    ->with('error', 'Tidak ada soal yang berhasil diimport.')
                    ->with('import_errors', $errors);
            }

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            \Illuminate\Support\Facades\Log::error('CSV import failed: ' . $e->getMessage(), [
                'exam_id' => $exam->id,
            ]);
            
            return redirect()->route('teacher.questions.index', $exam)
                ->with('error', 'Gagal mengimport soal. Silakan periksa format file CSV Anda.');
        } finally {
            if ($handle && is_resource($handle)) {
                @fclose($handle);
            }
        }
    }
}
