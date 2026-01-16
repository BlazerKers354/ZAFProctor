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
        // Tabel Kelas (misal: Kelas 1A, 1B, 2A, dst)
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama kelas: 1A, 1B, 2A, dst
            $table->string('grade_level')->nullable(); // Tingkat: 1, 2, 3, 4, 5, 6
            $table->text('description')->nullable();
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('users')->onDelete('set null'); // Wali kelas
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['name', 'grade_level']);
        });
        
        // Pivot table untuk siswa di kelas
        Schema::create('class_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('academic_year')->nullable(); // Tahun ajaran: 2025/2026
            $table->timestamps();
            
            $table->unique(['class_id', 'user_id', 'academic_year']);
        });

        // Tambah class_id ke users untuk siswa
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->after('role_id')->constrained('classes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
        });
        
        Schema::dropIfExists('class_student');
        Schema::dropIfExists('classes');
    }
};
