<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

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
     * Normalize image path before saving.
     */
    public function setQuestionImageAttribute($value): void
    {
        if (!is_string($value)) {
            $this->attributes['question_image'] = $value;
            return;
        }

        $value = trim($value);
        if ($value === '') {
            $this->attributes['question_image'] = null;
            return;
        }

        if (preg_match('/^https?:\/\//i', $value)) {
            $normalizedFromUrl = $this->extractNormalizedStoragePathFromUrl($value);
            if ($normalizedFromUrl !== null) {
                $this->attributes['question_image'] = $normalizedFromUrl;
                return;
            }

            $this->attributes['question_image'] = $value;
            return;
        }

        $this->attributes['question_image'] = $this->normalizeLocalStoragePath($value);
    }

    /**
     * Get normalized local path for image operations.
     */
    public function getNormalizedQuestionImagePathAttribute(): ?string
    {
        $path = $this->attributes['question_image'] ?? null;
        if (!is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);
        if (preg_match('/^https?:\/\//i', $path)) {
            return $this->extractNormalizedStoragePathFromUrl($path);
        }

        return $this->normalizeLocalStoragePath($path);
    }

    /**
     * Get question image URL for display.
     */
    public function getQuestionImageUrlAttribute(): ?string
    {
        $path = $this->attributes['question_image'] ?? null;
        if (!is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);
        if (preg_match('/^https?:\/\//i', $path)) {
            $normalizedFromUrl = $this->extractNormalizedStoragePathFromUrl($path);
            if ($normalizedFromUrl !== null) {
                return asset('storage/' . ltrim($normalizedFromUrl, '/'));
            }

            return $path;
        }

        $normalizedPath = $this->normalized_question_image_path;
        if (!$normalizedPath) {
            return null;
        }

        return asset('storage/' . ltrim($normalizedPath, '/'));
    }

    /**
     * Normalize local storage path to database-friendly format.
     */
    protected function normalizeLocalStoragePath(string $path): ?string
    {
        $path = trim($path);
        if ($path === '') {
            return null;
        }

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }

        return $path ?: null;
    }

    /**
     * Extract normalized local storage path from an absolute URL.
     */
    protected function extractNormalizedStoragePathFromUrl(string $url): ?string
    {
        $pathFromUrl = parse_url($url, PHP_URL_PATH);
        if (!is_string($pathFromUrl) || trim($pathFromUrl) === '') {
            return null;
        }

        $trimmedPath = ltrim(trim($pathFromUrl), '/');
        if (!str_starts_with($trimmedPath, 'storage/') && !str_starts_with($trimmedPath, 'public/')) {
            return null;
        }

        return $this->normalizeLocalStoragePath($trimmedPath);
    }

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
