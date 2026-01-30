<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProctoringLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'user_id',
        'violation_type',
        'description',
        'snapshot_path',
        'metadata',
        'severity',
        'is_reviewed',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_reviewed' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Violation type constants
     */
    public const TYPE_TAB_SWITCH = 'tab_switch';
    public const TYPE_FULLSCREEN_EXIT = 'fullscreen_exit';
    public const TYPE_CAMERA_DISABLED = 'camera_disabled';
    public const TYPE_NO_FACE_DETECTED = 'no_face_detected';
    public const TYPE_MULTIPLE_FACES = 'multiple_faces';
    public const TYPE_BROWSER_REFRESH = 'browser_refresh';
    public const TYPE_COPY_PASTE = 'copy_paste';
    public const TYPE_RIGHT_CLICK = 'right_click';
    public const TYPE_KEYBOARD_SHORTCUT = 'keyboard_shortcut';
    public const TYPE_WINDOW_BLUR = 'window_blur';
    public const TYPE_OTHER = 'other';

    /**
     * Severity constants
     */
    public const SEVERITY_LOW = 'low';
    public const SEVERITY_MEDIUM = 'medium';
    public const SEVERITY_HIGH = 'high';

    /**
     * Severity mapping for violation types
     */
    public static function getSeverityForType(string $type): string
    {
        $severityMap = [
            self::TYPE_TAB_SWITCH => self::SEVERITY_HIGH,
            self::TYPE_FULLSCREEN_EXIT => self::SEVERITY_MEDIUM,
            self::TYPE_CAMERA_DISABLED => self::SEVERITY_HIGH,
            self::TYPE_NO_FACE_DETECTED => self::SEVERITY_MEDIUM,
            self::TYPE_MULTIPLE_FACES => self::SEVERITY_HIGH,
            self::TYPE_BROWSER_REFRESH => self::SEVERITY_LOW,
            self::TYPE_COPY_PASTE => self::SEVERITY_HIGH,
            self::TYPE_RIGHT_CLICK => self::SEVERITY_LOW,
            self::TYPE_KEYBOARD_SHORTCUT => self::SEVERITY_MEDIUM,
            self::TYPE_WINDOW_BLUR => self::SEVERITY_MEDIUM,
        ];

        return $severityMap[$type] ?? self::SEVERITY_LOW;
    }

    /**
     * Get the attempt for this log
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    /**
     * Get the user for this log
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reviewer for this log
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get snapshot URL
     */
    public function getSnapshotUrlAttribute(): ?string
    {
        if (!$this->snapshot_path) {
            return null;
        }

        return Storage::disk('public')->url($this->snapshot_path);
    }

    /**
     * Mark as reviewed
     */
    public function markAsReviewed(int $reviewerId, ?string $notes = null): void
    {
        $this->is_reviewed = true;
        $this->reviewed_by = $reviewerId;
        $this->reviewed_at = now();
        $this->review_notes = $notes;
        $this->save();
    }

    /**
     * Scope for unreviewed logs
     */
    public function scopeUnreviewed($query)
    {
        return $query->where('is_reviewed', false);
    }

    /**
     * Scope for high severity
     */
    public function scopeHighSeverity($query)
    {
        return $query->where('severity', self::SEVERITY_HIGH);
    }

    /**
     * Get human readable violation type
     */
    public function getViolationLabelAttribute(): string
    {
        $labels = [
            self::TYPE_TAB_SWITCH => 'Pindah Tab',
            self::TYPE_FULLSCREEN_EXIT => 'Keluar Fullscreen',
            self::TYPE_CAMERA_DISABLED => 'Kamera Dimatikan',
            self::TYPE_NO_FACE_DETECTED => 'Wajah Tidak Terdeteksi',
            self::TYPE_MULTIPLE_FACES => 'Multiple Wajah',
            self::TYPE_BROWSER_REFRESH => 'Refresh Browser',
            self::TYPE_COPY_PASTE => 'Copy/Paste',
            self::TYPE_RIGHT_CLICK => 'Klik Kanan',
            self::TYPE_KEYBOARD_SHORTCUT => 'Shortcut Keyboard',
            self::TYPE_WINDOW_BLUR => 'Window Blur',
            self::TYPE_OTHER => 'Lainnya',
        ];

        return $labels[$this->violation_type] ?? $this->violation_type;
    }
}
