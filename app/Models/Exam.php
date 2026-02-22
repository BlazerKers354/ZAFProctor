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
        'type',
        'start_time',
        'end_time',
        'duration',
        'access_token',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
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
     * Get settings for this exam with default values
     */
    public function settings(): HasOne
    {
        return $this->hasOne(ExamSetting::class)->withDefault([
            'webcam_enabled' => true,
            'screen_capture_enabled' => true,
            'browser_lock_enabled' => true,
            'tab_switch_detection' => true,
            'max_tab_switches' => 5,
            'snapshot_interval' => 30,
            'shuffle_questions' => false,
            'shuffle_options' => false,
            'show_correct_answers' => false,
            'show_score' => true,
            'passing_score' => 60,
            'detect_face' => true,
            'detect_multiple_faces' => true,
            'detect_tab_switch' => true,
            'detect_fullscreen_exit' => true,
            'detect_copy_paste' => true,
            'detect_right_click' => true,
            'block_keyboard_shortcuts' => true,
            'warning_threshold' => 3,
            'auto_submit_threshold' => 5,
        ]);
    }

    /**
     * Check if exam is currently active
     */
    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED) {
            return false;
        }
        
        // For flexible exams (no start/end time), always active when published
        if ($this->type === 'flexible' || !$this->start_time || !$this->end_time) {
            return true;
        }
        
        $now = now();
        return $now->between($this->start_time, $this->end_time);
    }

    /**
     * Check if exam has started
     */
    public function hasStarted(): bool
    {
        // For flexible exams, always started when published
        if ($this->type === 'flexible' || !$this->start_time) {
            return $this->status === self::STATUS_PUBLISHED;
        }
        
        return now()->gte($this->start_time);
    }

    /**
     * Check if exam has ended
     */
    public function hasEnded(): bool
    {
        // For flexible exams, never ended (always available)
        if ($this->type === 'flexible' || !$this->end_time) {
            return false;
        }
        
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
            ->where(function($q) use ($now) {
                // Flexible exams (always active when published)
                $q->where('type', 'flexible')
                  ->orWhere(function($q2) use ($now) {
                      // Scheduled exams (within time range)
                      $q2->where('type', 'scheduled')
                         ->where('start_time', '<=', $now)
                         ->where('end_time', '>=', $now);
                  });
            });
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
