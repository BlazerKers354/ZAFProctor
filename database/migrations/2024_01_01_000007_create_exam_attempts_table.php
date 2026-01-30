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
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_remaining')->nullable(); // Sisa waktu dalam detik
            
            // Status
            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'graded', 'cancelled'])->default('not_started');
            $table->boolean('is_auto_submitted')->default(false);
            
            // Scores
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->boolean('is_passed')->nullable();
            
            // Proctoring
            $table->integer('violation_count')->default(0);
            $table->integer('tab_switch_count')->default(0);
            $table->integer('fullscreen_exit_count')->default(0);
            $table->boolean('camera_enabled')->default(false);
            
            // Client info
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            // No unique constraint - allows multiple attempts per user per exam
            $table->index(['exam_id', 'user_id']);
            $table->index('status');
            $table->index('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
