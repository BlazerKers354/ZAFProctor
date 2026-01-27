<?php

namespace App\Models;

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
        'screen_capture_enabled',
        'browser_lock_enabled',
        'tab_switch_detection',
        'max_tab_switches',
        'snapshot_interval',
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
        // Legacy fields (for backward compatibility)
        'detect_face',
        'detect_multiple_faces',
        'detect_tab_switch',
        'detect_fullscreen_exit',
        'detect_copy_paste',
        'detect_right_click',
        'block_keyboard_shortcuts',
        'warning_threshold',
        'auto_submit_threshold',
    ];

    protected $casts = [
        'webcam_enabled' => 'boolean',
        'screen_capture_enabled' => 'boolean',
        'browser_lock_enabled' => 'boolean',
        'tab_switch_detection' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_correct_answers' => 'boolean',
        'show_score' => 'boolean',
        // Legacy
        'detect_face' => 'boolean',
        'detect_multiple_faces' => 'boolean',
        'detect_tab_switch' => 'boolean',
        'detect_fullscreen_exit' => 'boolean',
        'detect_copy_paste' => 'boolean',
        'detect_right_click' => 'boolean',
        'block_keyboard_shortcuts' => 'boolean',
    ];

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
        ];
    }
}
