<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'type',
        'question',
        'question_image',
        'points',
        'order',
        'explanation',
    ];

    /**
     * Question type constants
     */
    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_ESSAY = 'essay';

    /**
     * Get the exam for this question
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get options for this question (multiple choice)
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    /**
     * Get answers for this question
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Check if question is multiple choice
     */
    public function isMultipleChoice(): bool
    {
        return $this->type === self::TYPE_MULTIPLE_CHOICE;
    }

    /**
     * Check if question is essay
     */
    public function isEssay(): bool
    {
        return $this->type === self::TYPE_ESSAY;
    }

    /**
     * Get correct option for multiple choice
     */
    public function getCorrectOption()
    {
        return $this->options()->where('is_correct', true)->first();
    }

    /**
     * Shuffle options for this question
     */
    public function getShuffledOptionsAttribute()
    {
        return $this->options->shuffle();
    }
}
