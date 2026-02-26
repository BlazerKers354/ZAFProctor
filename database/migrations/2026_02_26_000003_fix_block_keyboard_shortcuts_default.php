<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix block_keyboard_shortcuts that was silently set to false
     * because the checkbox was missing from the create/edit forms.
     */
    public function up(): void
    {
        // Reset all records to true (the intended default)
        // since there was no UI to control this field before
        DB::table('exam_settings')
            ->where('block_keyboard_shortcuts', false)
            ->update(['block_keyboard_shortcuts' => true]);
    }

    public function down(): void
    {
        // No rollback needed - this is a data fix
    }
};
