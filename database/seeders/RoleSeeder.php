<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Administrator sistem dengan akses penuh',
            ],
            [
                'name' => 'teacher',
                'display_name' => 'Guru',
                'description' => 'Guru yang dapat membuat dan mengelola ujian',
            ],
            [
                'name' => 'student',
                'display_name' => 'Siswa',
                'description' => 'Siswa yang dapat mengikuti ujian',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
