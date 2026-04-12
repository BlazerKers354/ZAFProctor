<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamAttempt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_id',
        'user_id',
        'started_at',
        'submitted_at',
        'time_remaining',
        'status',
        'is_auto_submitted',
        'score',
        'percentage',
        'is_passed',
        'violation_count',
        'tab_switch_count',
        'fullscreen_exit_count',
        'camera_enabled',
        'ip_address',
        'user_agent',
        'feedback',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'is_auto_submitted' => 'boolean',
        'is_passed' => 'boolean',
        'camera_enabled' => 'boolean',
        'score' => 'decimal:2',
        'percentage' => 'decimal:2',
    ];

    /**
     * Status constants
     */
    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_GRADED = 'graded';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the exam for this attempt
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the user for this attempt
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for user relationship (student)
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the finished_at attribute (alias for submitted_at)
     */
    public function getFinishedAtAttribute()
    {
        return $this->submitted_at;
    }

    /**
     * Get answers for this attempt
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'attempt_id');
    }

    /**
     * Get proctoring logs for this attempt
     */
    public function proctoringLogs(): HasMany
    {
        return $this->hasMany(ProctoringLog::class, 'attempt_id');
    }

    /**
     * Get the latest snapshot for this attempt
     */
    public function latestSnapshot(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProctoringLog::class, 'attempt_id')
            ->whereNotNull('snapshot_path')
            ->latest();
    }

    /**
     * Check if attempt is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if attempt is submitted
     */
    public function isSubmitted(): bool
    {
        return in_array($this->status, [self::STATUS_SUBMITTED, self::STATUS_GRADED]);
    }

    /**
     * Calculate remaining time in seconds
     */
    public function getRemainingTimeAttribute(): int
    {
        if (!$this->started_at || $this->isSubmitted()) {
            return 0;
        }

        $endTime = $this->started_at->addMinutes($this->exam->duration);
        $remaining = now()->diffInSeconds($endTime, false);

        return max(0, $remaining);
    }

    /**
     * Check if time has expired
     */
    public function hasTimeExpired(): bool
    {
        if (!$this->started_at) {
            return false;
        }

        $endTime = $this->started_at->addMinutes($this->exam->duration);
        return now()->gte($endTime);
    }

    /**
     * Get max violations allowed for this attempt
     */
    public function getMaxViolationsAttribute(): int
    {
        $settings = $this->exam->settings;
        return $settings?->auto_submit_threshold 
            ?? $settings?->max_tab_switches 
            ?? 5;
    }

    /**
     * Check if violations exceeded
     */
    public function hasExceededViolations(): bool
    {
        return $this->violation_count >= $this->max_violations;
    }

    /**
     * Calculate and update score
     */
    public function calculateScore(): void
    {
        $totalPoints = $this->exam->total_points;
        $earnedPoints = $this->answers()->sum('points_earned');

        $this->score = $earnedPoints;
        $this->percentage = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
        $this->is_passed = $this->percentage >= ($this->exam->settings?->passing_score ?? 60);
        $this->save();
    }

    /**
     * Scope for in progress attempts
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope for submitted attempts
     */
    public function scopeSubmitted($query)
    {
        return $query->whereIn('status', [self::STATUS_SUBMITTED, self::STATUS_GRADED]);
    }

    /**
     * Scope for graded attempts
     */
    public function scopeGraded($query)
    {
        return $query->where('status', self::STATUS_GRADED);
    }
}
