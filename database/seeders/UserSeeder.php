<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $teacherRole = Role::where('name', 'teacher')->first();
        $studentRole = Role::where('name', 'student')->first();

        // Create Admin
        User::updateOrCreate(
            ['email' => 'admin@zafproctor.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'is_approved' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Sample Teacher (approved)
        User::updateOrCreate(
            ['email' => 'guru@zafproctor.test'],
            [
                'name' => 'Ibu Guru Demo',
                'password' => Hash::make('password'),
                'role_id' => $teacherRole->id,
                'is_active' => true,
                'is_approved' => true,
                'approved_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // Create Sample Students (auto approved)
        $students = [
            ['name' => 'Ahmad Siswa', 'email' => 'siswa1@zafproctor.test', 'student_id' => 'SD001'],
            ['name' => 'Budi Pelajar', 'email' => 'siswa2@zafproctor.test', 'student_id' => 'SD002'],
            ['name' => 'Citra Murid', 'email' => 'siswa3@zafproctor.test', 'student_id' => 'SD003'],
        ];

        foreach ($students as $student) {
            User::updateOrCreate(
                ['email' => $student['email']],
                [
                    'name' => $student['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $studentRole->id,
                    'student_id' => $student['student_id'],
                    'is_active' => true,
                    'is_approved' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
