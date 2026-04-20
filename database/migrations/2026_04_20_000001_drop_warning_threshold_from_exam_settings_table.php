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
        if (!Schema::hasColumn('exam_settings', 'warning_threshold')) {
            return;
        }

        Schema::table('exam_settings', function (Blueprint $table) {
            $table->dropColumn('warning_threshold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('exam_settings', 'warning_threshold')) {
            return;
        }

        Schema::table('exam_settings', function (Blueprint $table) {
            $table->integer('warning_threshold')->default(3);
        });
    }
};
