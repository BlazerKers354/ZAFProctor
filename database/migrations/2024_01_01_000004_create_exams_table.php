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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            
            // Scheduling
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('duration_minutes'); // Durasi ujian dalam menit
            
            // Settings
            $table->string('access_token', 32)->unique(); // Token akses ujian
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('shuffle_answers')->default(true);
            $table->boolean('show_result')->default(false);
            $table->boolean('require_camera')->default(true);
            $table->boolean('require_fullscreen')->default(true);
            $table->integer('max_violations')->default(5); // Max pelanggaran sebelum auto-submit
            $table->integer('passing_score')->default(60); // Nilai minimum lulus
            
            // Status
            $table->enum('status', ['draft', 'published', 'ongoing', 'completed', 'cancelled'])->default('draft');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('course_id');
            $table->index('created_by');
            $table->index('status');
            $table->index(['start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
