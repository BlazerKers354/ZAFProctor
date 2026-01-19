<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exam_settings', function (Blueprint $table) {
            // Add new proctoring fields
            if (!Schema::hasColumn('exam_settings', 'webcam_enabled')) {
                $table->boolean('webcam_enabled')->default(true)->after('exam_id');
            }
            if (!Schema::hasColumn('exam_settings', 'screen_capture_enabled')) {
                $table->boolean('screen_capture_enabled')->default(true)->after('webcam_enabled');
            }
            if (!Schema::hasColumn('exam_settings', 'browser_lock_enabled')) {
                $table->boolean('browser_lock_enabled')->default(true)->after('screen_capture_enabled');
            }
            if (!Schema::hasColumn('exam_settings', 'tab_switch_detection')) {
                $table->boolean('tab_switch_detection')->default(true)->after('browser_lock_enabled');
            }
            if (!Schema::hasColumn('exam_settings', 'max_tab_switches')) {
                $table->integer('max_tab_switches')->default(3)->after('tab_switch_detection');
            }
            
            // Add new display fields
            if (!Schema::hasColumn('exam_settings', 'shuffle_questions')) {
                $table->boolean('shuffle_questions')->default(false)->after('max_tab_switches');
            }
            if (!Schema::hasColumn('exam_settings', 'shuffle_options')) {
                $table->boolean('shuffle_options')->default(false)->after('shuffle_questions');
            }
            if (!Schema::hasColumn('exam_settings', 'show_correct_answers')) {
                $table->boolean('show_correct_answers')->default(false)->after('shuffle_options');
            }
            if (!Schema::hasColumn('exam_settings', 'show_score')) {
                $table->boolean('show_score')->default(true)->after('show_correct_answers');
            }
            
            // Add new attempt fields
            if (!Schema::hasColumn('exam_settings', 'max_attempts')) {
                $table->integer('max_attempts')->nullable()->after('show_score');
            }
            if (!Schema::hasColumn('exam_settings', 'grade_method')) {
                $table->enum('grade_method', ['highest', 'latest', 'average'])->default('highest')->after('max_attempts');
            }
            
            // Add passing score
            if (!Schema::hasColumn('exam_settings', 'passing_score')) {
                $table->integer('passing_score')->default(60)->after('grade_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_settings', function (Blueprint $table) {
            $columnsToRemove = [
                'webcam_enabled',
                'screen_capture_enabled',
                'browser_lock_enabled',
                'tab_switch_detection',
                'max_tab_switches',
                'shuffle_questions',
                'shuffle_options',
                'show_correct_answers',
                'show_score',
                'max_attempts',
                'grade_method',
                'passing_score'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('exam_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
