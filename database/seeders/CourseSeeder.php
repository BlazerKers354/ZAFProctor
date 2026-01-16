<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = User::whereHas('role', fn($q) => $q->where('name', 'teacher'))->first();
        $students = User::whereHas('role', fn($q) => $q->where('name', 'student'))->get();

        if (!$teacher) {
            return;
        }

        $courses = [
            [
                'code' => 'IF101',
                'name' => 'Pemrograman Dasar',
                'description' => 'Mata kuliah dasar pemrograman menggunakan bahasa Python',
            ],
            [
                'code' => 'IF201',
                'name' => 'Struktur Data',
                'description' => 'Konsep dan implementasi struktur data',
            ],
            [
                'code' => 'IF301',
                'name' => 'Basis Data',
                'description' => 'Perancangan dan implementasi basis data relasional',
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::updateOrCreate(
                ['code' => $courseData['code']],
                array_merge($courseData, ['teacher_id' => $teacher->id])
            );

            // Attach students to course
            $course->students()->sync($students->pluck('id'));
        }
    }
}
