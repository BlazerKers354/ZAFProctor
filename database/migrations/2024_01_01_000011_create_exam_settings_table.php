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
        Schema::create('exam_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            
            // Proctoring settings
            $table->integer('snapshot_interval')->default(30); // Interval capture dalam detik
            $table->boolean('detect_face')->default(true);
            $table->boolean('detect_multiple_faces')->default(true);
            $table->boolean('detect_tab_switch')->default(true);
            $table->boolean('detect_fullscreen_exit')->default(true);
            $table->boolean('detect_copy_paste')->default(true);
            $table->boolean('detect_right_click')->default(true);
            $table->boolean('block_keyboard_shortcuts')->default(true);
            
            // Warning thresholds
            $table->integer('warning_threshold')->default(3); // Peringatan setelah x pelanggaran
            $table->integer('auto_submit_threshold')->default(5); // Auto-submit setelah x pelanggaran
            
            $table->timestamps();
            
            $table->unique('exam_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_settings');
    }
};
