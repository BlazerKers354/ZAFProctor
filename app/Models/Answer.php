<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_id',
        'essay_answer',
        'is_correct',
        'points_earned',
        'feedback',
        'answered_at',
    ];

    protected $casts = [
        'attempt_id' => 'integer',
        'question_id' => 'integer',
        'selected_option_id' => 'integer',
        'is_correct' => 'boolean',
        'points_earned' => 'decimal:2',
        'answered_at' => 'datetime',
    ];

    /**
     * Get the attempt for this answer
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    /**
     * Get the question for this answer
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the selected option for this answer
     */
    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    /**
     * Grade this answer (for multiple choice)
     */
    public function grade(): void
    {
        if ($this->question->isMultipleChoice()) {
            $correctOption = $this->question->getCorrectOption();
            $this->is_correct = $correctOption && (int) $this->selected_option_id === (int) $correctOption->id;
            $this->points_earned = $this->is_correct ? $this->question->points : 0;
            $this->save();
        }
    }

    /**
     * Set essay grade manually
     */
    public function gradeEssay(float $points, ?string $feedback = null): void
    {
        $maxPoints = $this->question->points;
        $this->points_earned = min($points, $maxPoints);
        $this->is_correct = $this->points_earned >= ($maxPoints * 0.5); // 50% threshold
        $this->feedback = $feedback;
        $this->save();
    }
}
