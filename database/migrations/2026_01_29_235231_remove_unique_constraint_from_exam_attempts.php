<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration removes the unique constraint on exam_attempts to allow
     * multiple attempts per user per exam (for max_attempts feature).
     * 
     * Note: For fresh installs, the original migration has been updated to not
     * include this unique constraint. This migration handles existing databases.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        $hasUniqueConstraint = $this->checkIfUniqueConstraintExists();
        
        if (!$hasUniqueConstraint) {
            // Constraint doesn't exist (fresh install), skip
            return;
        }
        
        if ($driver === 'mysql') {
            // Disable foreign key checks for MySQL
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            try {
                Schema::table('exam_attempts', function (Blueprint $table) {
                    $table->dropUnique(['exam_id', 'user_id']);
                });
            } catch (\Exception $e) {
                // Try raw SQL if Blueprint fails
                try {
                    DB::statement('ALTER TABLE exam_attempts DROP INDEX exam_attempts_exam_id_user_id_unique');
                } catch (\Exception $e2) {
                    // Ignore if constraint doesn't exist
                }
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } else {
            // For other databases (PostgreSQL, SQLite, etc.)
            try {
                Schema::table('exam_attempts', function (Blueprint $table) {
                    $table->dropUnique(['exam_id', 'user_id']);
                });
            } catch (\Exception $e) {
                // Ignore if constraint doesn't exist
            }
        }
        
        // Add a regular index for performance if it doesn't exist
        if (!$this->checkIfIndexExists('exam_attempts_exam_user_index')) {
            Schema::table('exam_attempts', function (Blueprint $table) {
                $table->index(['exam_id', 'user_id'], 'exam_attempts_exam_user_index');
            });
        }
    }

    /**
     * Check if the unique constraint exists.
     */
    private function checkIfUniqueConstraintExists(): bool
    {
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            $result = DB::select("SHOW INDEX FROM exam_attempts WHERE Key_name = 'exam_attempts_exam_id_user_id_unique'");
            return count($result) > 0;
        } elseif ($driver === 'pgsql') {
            $result = DB::select("SELECT 1 FROM pg_indexes WHERE indexname = 'exam_attempts_exam_id_user_id_unique'");
            return count($result) > 0;
        }
        
        return true; // Assume it exists for other drivers
    }
    
    /**
     * Check if index exists.
     */
    private function checkIfIndexExists(string $indexName): bool
    {
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            $result = DB::select("SHOW INDEX FROM exam_attempts WHERE Key_name = ?", [$indexName]);
            return count($result) > 0;
        } elseif ($driver === 'pgsql') {
            $result = DB::select("SELECT 1 FROM pg_indexes WHERE indexname = ?", [$indexName]);
            return count($result) > 0;
        }
        
        return false;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only rollback if the index exists
        if ($this->checkIfIndexExists('exam_attempts_exam_user_index')) {
            Schema::table('exam_attempts', function (Blueprint $table) {
                $table->dropIndex('exam_attempts_exam_user_index');
            });
        }
        
        // Re-add the unique constraint
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->unique(['exam_id', 'user_id']);
        });
    }
};
