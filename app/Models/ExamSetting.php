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
        'snapshot_interval',
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
            'snapshot_interval' => 30,
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
