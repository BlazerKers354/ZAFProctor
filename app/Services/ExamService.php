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
            // Check if user already has an attempt
            $existingAttempt = ExamAttempt::where('exam_id', $exam->id)
                ->where('user_id', $userId)
                ->first();

            if ($existingAttempt && $existingAttempt->isInProgress()) {
                return $existingAttempt;
            }

            if ($existingAttempt && $existingAttempt->isSubmitted()) {
                throw new \Exception('Anda sudah mengerjakan ujian ini.');
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
            $questions = $questions->shuffle();
        }

        if ($exam->settings?->shuffle_options) {
            $questions = $questions->map(function ($question) {
                if ($question->isMultipleChoice()) {
                    $question->setRelation('options', $question->options->shuffle());
                }
                return $question;
            });
        }

        return $questions;
    }

    /**
     * Save an answer
     */
    public function saveAnswer(ExamAttempt $attempt, int $questionId, ?int $optionId = null, ?string $essayAnswer = null): Answer
    {
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
     * Submit exam
     */
    public function submitExam(ExamAttempt $attempt, bool $isAutoSubmit = false): ExamAttempt
    {
        return DB::transaction(function () use ($attempt, $isAutoSubmit) {
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
        $maxViolations = $attempt->exam->settings?->auto_submit_threshold 
            ?? $attempt->exam->max_violations;

        return $attempt->violation_count >= $maxViolations;
    }

    /**
     * Get exam statistics
     */
    public function getExamStatistics(Exam $exam): array
    {
        $attempts = $exam->attempts()->submitted()->get();

        if ($attempts->isEmpty()) {
            return [
                'total_attempts' => 0,
                'average_score' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
                'pass_rate' => 0,
                'auto_submit_count' => 0,
            ];
        }

        return [
            'total_attempts' => $attempts->count(),
            'average_score' => round($attempts->avg('percentage'), 2),
            'highest_score' => round($attempts->max('percentage'), 2),
            'lowest_score' => round($attempts->min('percentage'), 2),
            'pass_rate' => round($attempts->where('is_passed', true)->count() / $attempts->count() * 100, 2),
            'auto_submit_count' => $attempts->where('is_auto_submitted', true)->count(),
        ];
    }
}
