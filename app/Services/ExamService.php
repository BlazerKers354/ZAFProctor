<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\AuditLog;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExamService
{
    /**
     * Start an exam attempt for a user
     */
    public function startExam(Exam $exam, int $userId, string $ipAddress, string $userAgent): ExamAttempt
    {
        return DB::transaction(function () use ($exam, $userId, $ipAddress, $userAgent) {
            // Get all user attempts for this exam with pessimistic locking
            $existingAttempts = ExamAttempt::where('exam_id', $exam->id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->get();

            // Check if user has an in-progress attempt
            $inProgressAttempt = $existingAttempts->first(fn($a) => $a->isInProgress());
            if ($inProgressAttempt) {
                return $inProgressAttempt;
            }

            // Count submitted attempts
            $submittedCount = $existingAttempts->filter(fn($a) => $a->isSubmitted())->count();
            
            // Get max attempts setting (null or 0 = unlimited)
            $maxAttempts = $exam->settings?->max_attempts;
            
            // Check if user has reached max attempts (only if max_attempts is set and > 0)
            if ($maxAttempts !== null && $maxAttempts > 0 && $submittedCount >= $maxAttempts) {
                throw new \Exception('Anda sudah mencapai batas maksimal percobaan untuk ujian ini.');
            }

            // Create new attempt
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => $userId,
                'started_at' => now(),
                'status' => ExamAttempt::STATUS_IN_PROGRESS,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'camera_enabled' => $exam->settings?->webcam_enabled ?? true,
            ]);

            // Log the action
            AuditLog::log(
                AuditLog::ACTION_EXAM_START,
                "Started exam: {$exam->title}",
                ExamAttempt::class,
                $attempt->id
            );

            return $attempt;
        });
    }

    /**
     * Get shuffled questions for an exam
     */
    public function getQuestionsForAttempt(Exam $exam, ExamAttempt $attempt): Collection
    {
        $questions = $exam->questions()->with('options')->get();

        if ($exam->settings?->shuffle_questions) {
            // Use attempt ID as seed for consistent shuffle per attempt
            $questions = $this->seededShuffle($questions, $attempt->id);
        }

        if ($exam->settings?->shuffle_options) {
            $questions = $questions->map(function ($question) use ($attempt) {
                if ($question->isMultipleChoice()) {
                    // Use combination of attempt_id + question_id for consistent option shuffle
                    $seed = $attempt->id + $question->id;
                    $question->setRelation('options', $this->seededShuffle($question->options, $seed));
                }
                return $question;
            });
        }

        return $questions;
    }

    /**
     * Shuffle a collection with a consistent seed
     */
    protected function seededShuffle(Collection $collection, int $seed): Collection
    {
        $items = $collection->all();
        mt_srand($seed);
        
        for ($i = count($items) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            $temp = $items[$i];
            $items[$i] = $items[$j];
            $items[$j] = $temp;
        }
        
        // Reset random seed
        mt_srand();
        
        return collect($items);
    }

    /**
     * Save an answer with validation
     */
    public function saveAnswer(ExamAttempt $attempt, int $questionId, ?int $optionId = null, ?string $essayAnswer = null): Answer
    {
        // Validate attempt is still in progress
        if (!$attempt->isInProgress()) {
            throw new \Exception('Ujian sudah selesai. Tidak dapat menyimpan jawaban.');
        }
        
        // Validate question belongs to this exam
        $questionExists = $attempt->exam->questions()->where('id', $questionId)->exists();
        if (!$questionExists) {
            throw new \Exception('Soal tidak valid untuk ujian ini.');
        }
        
        $answer = Answer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
            ],
            [
                'selected_option_id' => $optionId,
                'essay_answer' => $essayAnswer,
                'answered_at' => now(),
            ]
        );

        return $answer;
    }

    /**
     * Submit exam with double-submit protection
     */
    public function submitExam(ExamAttempt $attempt, bool $isAutoSubmit = false): ExamAttempt
    {
        return DB::transaction(function () use ($attempt, $isAutoSubmit) {
            // Lock the attempt to prevent race condition
            $attempt = ExamAttempt::where('id', $attempt->id)
                ->lockForUpdate()
                ->first();
            
            // Check if already submitted (prevent double submit)
            if ($attempt->isSubmitted()) {
                return $attempt;
            }
            
            // Grade all multiple choice answers
            $this->gradeMultipleChoiceAnswers($attempt);

            // Calculate total score
            $attempt->calculateScore();

            // Update attempt status
            $attempt->update([
                'status' => ExamAttempt::STATUS_SUBMITTED,
                'submitted_at' => now(),
                'is_auto_submitted' => $isAutoSubmit,
                'time_remaining' => $attempt->remaining_time,
            ]);

            // Log the action
            AuditLog::log(
                AuditLog::ACTION_EXAM_SUBMIT,
                "Submitted exam: {$attempt->exam->title}" . ($isAutoSubmit ? ' (auto)' : ''),
                ExamAttempt::class,
                $attempt->id
            );

            return $attempt->fresh();
        });
    }

    /**
     * Grade multiple choice answers
     */
    protected function gradeMultipleChoiceAnswers(ExamAttempt $attempt): void
    {
        $answers = $attempt->answers()
            ->whereHas('question', function ($query) {
                $query->where('type', Question::TYPE_MULTIPLE_CHOICE);
            })
            ->get();

        foreach ($answers as $answer) {
            $answer->grade();
        }
    }

    /**
     * Grade an essay answer
     */
    public function gradeEssayAnswer(Answer $answer, float $points, ?string $feedback = null): Answer
    {
        $answer->gradeEssay($points, $feedback);
        
        // Recalculate attempt score
        $answer->attempt->calculateScore();

        return $answer;
    }

    /**
     * Check if exam should be auto-submitted due to violations
     */
    public function shouldAutoSubmitDueToViolations(ExamAttempt $attempt): bool
    {
        $settings = $attempt->exam->settings;
        $maxViolations = $settings?->auto_submit_threshold 
            ?? $settings?->max_tab_switches 
            ?? 5;

        return $attempt->violation_count >= $maxViolations;
    }

    /**
     * Get exam statistics
     */
    public function getExamStatistics(Exam $exam): array
    {
        $allAttempts = $exam->attempts()->get();
        $submittedAttempts = $exam->attempts()->whereIn('status', ['completed', 'submitted'])->get();

        if ($submittedAttempts->isEmpty()) {
            return [
                'total_participants' => $allAttempts->count(),
                'total_attempts' => 0,
                'completed_attempts' => 0,
                'average_score' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
                'pass_rate' => 0,
                'auto_submit_count' => 0,
            ];
        }

        $passingScore = $exam->settings?->passing_score ?? 60;

        return [
            'total_participants' => $allAttempts->count(),
            'total_attempts' => $submittedAttempts->count(),
            'completed_attempts' => $submittedAttempts->count(),
            'average_score' => round($submittedAttempts->avg('score') ?? 0, 2),
            'highest_score' => round($submittedAttempts->max('score') ?? 0, 2),
            'lowest_score' => round($submittedAttempts->min('score') ?? 0, 2),
            'pass_rate' => round($submittedAttempts->where('percentage', '>=', $passingScore)->count() / $submittedAttempts->count() * 100, 2),
            'auto_submit_count' => $submittedAttempts->where('is_auto_submitted', true)->count(),
        ];
    }
}
