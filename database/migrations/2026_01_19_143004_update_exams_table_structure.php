<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Add new columns
            if (!Schema::hasColumn('exams', 'type')) {
                $table->enum('type', ['scheduled', 'flexible'])->default('scheduled')->after('description');
            }
            
            if (!Schema::hasColumn('exams', 'duration')) {
                $table->integer('duration')->default(60)->after('type'); // Durasi dalam menit
            }
        });
        
        // Copy data from duration_minutes to duration if exists
        if (Schema::hasColumn('exams', 'duration_minutes')) {
            DB::statement('UPDATE exams SET duration = duration_minutes WHERE duration_minutes IS NOT NULL');
        }
        
        Schema::table('exams', function (Blueprint $table) {
            // Modify existing columns to be nullable
            $table->dateTime('start_time')->nullable()->change();
            $table->dateTime('end_time')->nullable()->change();
            
            // Remove old columns if they exist
            if (Schema::hasColumn('exams', 'duration_minutes')) {
                $table->dropColumn('duration_minutes');
            }
            
            if (Schema::hasColumn('exams', 'instructions')) {
                $table->dropColumn('instructions');
            }
            
            // Remove old settings columns (moved to exam_settings table)
            $columnsToRemove = [
                'shuffle_questions',
                'shuffle_answers', 
                'show_result',
                'require_camera',
                'require_fullscreen',
                'max_violations',
                'passing_score'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('exams', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Restore old columns
            $table->text('instructions')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_answers')->default(true);
            $table->boolean('show_result')->default(false);
            $table->boolean('require_camera')->default(true);
            $table->boolean('require_fullscreen')->default(true);
            $table->integer('max_violations')->default(5);
            $table->integer('passing_score')->default(60);
            
            // Remove new columns
            if (Schema::hasColumn('exams', 'type')) {
                $table->dropColumn('type');
            }
            
            if (Schema::hasColumn('exams', 'duration')) {
                $table->dropColumn('duration');
            }
            
            // Restore NOT NULL on datetime columns
            $table->dateTime('start_time')->nullable(false)->change();
            $table->dateTime('end_time')->nullable(false)->change();
        });
    }
};
