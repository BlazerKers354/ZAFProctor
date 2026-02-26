<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        // Proctoring
        'webcam_enabled',
        'browser_lock_enabled',
        'tab_switch_detection',
        'max_tab_switches',
        'snapshot_interval',
        'block_keyboard_shortcuts',
        // Display
        'shuffle_questions',
        'shuffle_options',
        'show_correct_answers',
        'show_score',
        // Attempts
        'max_attempts',
        'grade_method',
        // Additional
        'passing_score',
        'warning_threshold',
        'auto_submit_threshold',
    ];

    protected $casts = [
        'webcam_enabled' => 'boolean',
        'browser_lock_enabled' => 'boolean',
        'tab_switch_detection' => 'boolean',
        'max_tab_switches' => 'integer',
        'snapshot_interval' => 'integer',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_correct_answers' => 'boolean',
        'show_score' => 'boolean',
        'max_attempts' => 'integer',
        'passing_score' => 'integer',
        'warning_threshold' => 'integer',
        'auto_submit_threshold' => 'integer',
        'block_keyboard_shortcuts' => 'boolean',
    ];

    // ── Backward-compatible accessors ──────────────────────────
    // Legacy detect_* fields now map to consolidated settings.
    // Blade views and ProctoringController can still read these.

    protected function detectFace(): Attribute
    {
        return Attribute::get(fn () => $this->webcam_enabled ?? true);
    }

    protected function detectMultipleFaces(): Attribute
    {
        return Attribute::get(fn () => $this->webcam_enabled ?? true);
    }

    protected function detectTabSwitch(): Attribute
    {
        return Attribute::get(fn () => $this->tab_switch_detection ?? true);
    }

    protected function detectFullscreenExit(): Attribute
    {
        return Attribute::get(fn () => $this->browser_lock_enabled ?? true);
    }

    protected function detectCopyPaste(): Attribute
    {
        return Attribute::get(fn () => $this->block_keyboard_shortcuts ?? true);
    }

    protected function detectRightClick(): Attribute
    {
        return Attribute::get(fn () => $this->block_keyboard_shortcuts ?? true);
    }

    protected function screenCaptureEnabled(): Attribute
    {
        return Attribute::get(fn () => false); // Feature not implemented
    }

    /**
     * Get the exam for this setting
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get default settings
     */
    public static function getDefaults(): array
    {
        return [
            'webcam_enabled' => true,
            'browser_lock_enabled' => true,
            'tab_switch_detection' => true,
            'max_tab_switches' => 5,
            'snapshot_interval' => 30,
            'shuffle_questions' => false,
            'shuffle_options' => false,
            'show_correct_answers' => false,
            'show_score' => true,
            'passing_score' => 60,
            'block_keyboard_shortcuts' => true,
            'warning_threshold' => 3,
            'auto_submit_threshold' => 5,
        ];
    }
}
