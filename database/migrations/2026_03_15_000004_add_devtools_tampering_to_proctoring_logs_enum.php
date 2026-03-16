<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE proctoring_logs MODIFY violation_type ENUM('tab_switch','fullscreen_exit','camera_disabled','no_face_detected','multiple_faces','browser_refresh','copy_paste','right_click','keyboard_shortcut','window_blur','devtools','tampering','other') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE proctoring_logs MODIFY violation_type ENUM('tab_switch','fullscreen_exit','camera_disabled','no_face_detected','multiple_faces','browser_refresh','copy_paste','right_click','keyboard_shortcut','window_blur','other') NOT NULL");
    }
};
