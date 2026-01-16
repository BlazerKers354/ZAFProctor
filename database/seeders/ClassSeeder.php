<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            ['name' => '1A', 'grade_level' => '1', 'description' => 'Kelas 1 Paralel A'],
            ['name' => '1B', 'grade_level' => '1', 'description' => 'Kelas 1 Paralel B'],
            ['name' => '2A', 'grade_level' => '2', 'description' => 'Kelas 2 Paralel A'],
            ['name' => '2B', 'grade_level' => '2', 'description' => 'Kelas 2 Paralel B'],
            ['name' => '3A', 'grade_level' => '3', 'description' => 'Kelas 3 Paralel A'],
            ['name' => '3B', 'grade_level' => '3', 'description' => 'Kelas 3 Paralel B'],
            ['name' => '4A', 'grade_level' => '4', 'description' => 'Kelas 4 Paralel A'],
            ['name' => '4B', 'grade_level' => '4', 'description' => 'Kelas 4 Paralel B'],
            ['name' => '5A', 'grade_level' => '5', 'description' => 'Kelas 5 Paralel A'],
            ['name' => '5B', 'grade_level' => '5', 'description' => 'Kelas 5 Paralel B'],
            ['name' => '6A', 'grade_level' => '6', 'description' => 'Kelas 6 Paralel A'],
            ['name' => '6B', 'grade_level' => '6', 'description' => 'Kelas 6 Paralel B'],
        ];

        foreach ($classes as $class) {
            SchoolClass::updateOrCreate(
                ['name' => $class['name'], 'grade_level' => $class['grade_level']],
                $class
            );
        }
    }
}
