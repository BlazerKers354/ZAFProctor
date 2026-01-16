<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'created_by',
        'title',
        'description',
        'instructions',
        'start_time',
        'end_time',
        'duration_minutes',
        'access_token',
        'shuffle_questions',
        'shuffle_answers',
        'show_result',
        'require_camera',
        'require_fullscreen',
        'max_violations',
        'passing_score',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
        'show_result' => 'boolean',
        'require_camera' => 'boolean',
        'require_fullscreen' => 'boolean',
    ];

    /**
     * Status constants
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($exam) {
            if (empty($exam->access_token)) {
                $exam->access_token = Str::random(32);
            }
        });
    }

    /**
     * Get the course for this exam
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the creator of this exam
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get questions for this exam
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    /**
     * Get attempts for this exam
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * Get settings for this exam
     */
    public function settings(): HasOne
    {
        return $this->hasOne(ExamSetting::class);
    }

    /**
     * Check if exam is currently active
     */
    public function isActive(): bool
    {
        $now = now();
        return $this->status === self::STATUS_PUBLISHED 
            && $now->between($this->start_time, $this->end_time);
    }

    /**
     * Check if exam has started
     */
    public function hasStarted(): bool
    {
        return now()->gte($this->start_time);
    }

    /**
     * Check if exam has ended
     */
    public function hasEnded(): bool
    {
        return now()->gte($this->end_time);
    }

    /**
     * Get total points for this exam
     */
    public function getTotalPointsAttribute(): int
    {
        return $this->questions()->sum('points');
    }

    /**
     * Get total questions count
     */
    public function getQuestionCountAttribute(): int
    {
        return $this->questions()->count();
    }

    /**
     * Scope for published exams
     */
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope for active exams (within time range)
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now);
    }

    /**
     * Scope for upcoming exams
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('start_time', '>', now());
    }

    /**
     * Generate new access token
     */
    public function regenerateToken(): string
    {
        $this->access_token = Str::random(32);
        $this->save();
        return $this->access_token;
    }
}
