<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('exam_settings')) {
            return;
        }

        if (!Schema::hasColumn('exam_settings', 'auto_submit_threshold') || !Schema::hasColumn('exam_settings', 'max_tab_switches')) {
            return;
        }

        DB::table('exam_settings')->update([
            'max_tab_switches' => DB::raw('COALESCE(auto_submit_threshold, max_tab_switches, 5)'),
            'auto_submit_threshold' => DB::raw('COALESCE(auto_submit_threshold, max_tab_switches, 5)'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: synchronization is forward-only to protect existing data integrity.
    }
};
