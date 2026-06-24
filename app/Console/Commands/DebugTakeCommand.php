<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExamAttempt;
use App\Services\ExamService;

class DebugTakeCommand extends Command
{
    protected $signature = 'debug:take {attempt_id}';
    protected $description = 'Debug take exam';

    public function handle(ExamService $examService)
    {
        $attempt = ExamAttempt::find($this->argument('attempt_id'));
        if (!$attempt) {
            $this->error('Attempt not found');
            return;
        }
        
        $this->info('Attempt found. Loading relations...');
        
        try {
            $attempt->load(['exam.settings', 'answers']);
            $this->info('Relations loaded.');
            
            $questions = $examService->getQuestionsForAttempt($attempt->exam, $attempt);
            $this->info('Questions retrieved: ' . $questions->count());
            
            $answeredQuestions = $attempt->answers->keyBy('question_id');
            $this->info('Answers keyed: ' . $answeredQuestions->count());
            
            $view = view('student.exams.take', compact('attempt', 'questions', 'answeredQuestions'))->render();
            $this->info('View rendered successfully, length: ' . strlen($view));
        } catch (\Throwable $e) {
            $this->error('Error occurred: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
            $this->error($e->getTraceAsString());
        }
    }
}
