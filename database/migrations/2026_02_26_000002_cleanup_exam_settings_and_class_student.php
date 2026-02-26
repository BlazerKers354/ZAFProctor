<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Cleans up legacy/duplicate proctoring fields from exam_settings:
     * - screen_capture_enabled  → removed (not implemented)
     * - detect_face             → merged into webcam_enabled
     * - detect_multiple_faces   → merged into webcam_enabled
     * - detect_tab_switch       → merged into tab_switch_detection
     * - detect_fullscreen_exit  → merged into browser_lock_enabled
     * - detect_copy_paste       → merged into block_keyboard_shortcuts
     * - detect_right_click      → merged into block_keyboard_shortcuts
     * 
     * Also removes unused class_student pivot table.
     */
    public function up(): void
    {
        Schema::table('exam_settings', function (Blueprint $table) {
            $columns = [
                'screen_capture_enabled',
                'detect_face',
                'detect_multiple_faces',
                'detect_tab_switch',
                'detect_fullscreen_exit',
                'detect_copy_paste',
                'detect_right_click',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('exam_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('class_student');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_settings', 'screen_capture_enabled')) {
                $table->boolean('screen_capture_enabled')->default(true)->after('webcam_enabled');
            }
            if (!Schema::hasColumn('exam_settings', 'detect_face')) {
                $table->boolean('detect_face')->default(true)->after('auto_submit_threshold');
            }
            if (!Schema::hasColumn('exam_settings', 'detect_multiple_faces')) {
                $table->boolean('detect_multiple_faces')->default(true)->after('detect_face');
            }
            if (!Schema::hasColumn('exam_settings', 'detect_tab_switch')) {
                $table->boolean('detect_tab_switch')->default(true)->after('detect_multiple_faces');
            }
            if (!Schema::hasColumn('exam_settings', 'detect_fullscreen_exit')) {
                $table->boolean('detect_fullscreen_exit')->default(true)->after('detect_tab_switch');
            }
            if (!Schema::hasColumn('exam_settings', 'detect_copy_paste')) {
                $table->boolean('detect_copy_paste')->default(true)->after('detect_fullscreen_exit');
            }
            if (!Schema::hasColumn('exam_settings', 'detect_right_click')) {
                $table->boolean('detect_right_click')->default(true)->after('detect_copy_paste');
            }
        });

        Schema::create('class_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('academic_year')->nullable();
            $table->timestamps();
            $table->unique(['class_id', 'user_id', 'academic_year']);
        });
    }
};
