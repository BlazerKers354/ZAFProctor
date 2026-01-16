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
        Schema::create('proctoring_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Violation type
            $table->enum('violation_type', [
                'tab_switch',           // Pindah tab browser
                'fullscreen_exit',      // Keluar dari fullscreen
                'camera_disabled',      // Kamera dimatikan
                'no_face_detected',     // Wajah tidak terdeteksi
                'multiple_faces',       // Lebih dari satu wajah
                'browser_refresh',      // Refresh halaman
                'copy_paste',           // Copy/paste terdeteksi
                'right_click',          // Klik kanan
                'keyboard_shortcut',    // Shortcut keyboard mencurigakan
                'window_blur',          // Window kehilangan fokus
                'other'                 // Lainnya
            ]);
            
            $table->text('description')->nullable();
            $table->string('snapshot_path')->nullable(); // Path ke snapshot kamera
            $table->json('metadata')->nullable(); // Data tambahan
            $table->string('severity')->default('medium'); // low, medium, high
            $table->boolean('is_reviewed')->default(false);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            
            $table->timestamps();
            
            $table->index('attempt_id');
            $table->index('user_id');
            $table->index('violation_type');
            $table->index('severity');
            $table->index('is_reviewed');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proctoring_logs');
    }
};
