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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, teacher, student
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'System administrator with full access', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'teacher', 'display_name' => 'Pengawas/Dosen', 'description' => 'Teacher or exam proctor', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'student', 'display_name' => 'Peserta Ujian', 'description' => 'Exam participant', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
