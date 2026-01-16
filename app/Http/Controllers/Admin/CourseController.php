<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request): View
    {
        $query = Course::with('teacher')->withCount(['students', 'exams']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('code', 'ilike', "%{$search}%");
            });
        }

        // Filter by teacher
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $courses = $query->latest()->paginate(15);
        $teachers = User::byRole(Role::TEACHER)->get();

        return view('admin.courses.index', compact('courses', 'teachers'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): View
    {
        $teachers = User::byRole(Role::TEACHER)->active()->get();
        return view('admin.courses.create', compact('teachers'));
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:courses'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'teacher_id' => ['required', 'exists:users,id'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Course::create($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Mata pelajaran berhasil dibuat.');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course): View
    {
        $course->load(['teacher', 'students', 'exams.creator']);

        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): View
    {
        $teachers = User::byRole(Role::TEACHER)->active()->get();
        return view('admin.courses.edit', compact('course', 'teachers'));
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:courses,code,' . $course->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'teacher_id' => ['required', 'exists:users,id'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $course->update($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    /**
     * Manage students in a course.
     */
    public function students(Course $course): View
    {
        $course->load('students');
        $availableStudents = User::byRole(Role::STUDENT)
            ->active()
            ->whereNotIn('id', $course->students->pluck('id'))
            ->get();

        return view('admin.courses.students', compact('course', 'availableStudents'));
    }

    /**
     * Add students to a course.
     */
    public function addStudents(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['exists:users,id'],
        ]);

        $course->students()->syncWithoutDetaching($validated['student_ids']);

        return back()->with('success', 'Siswa berhasil ditambahkan ke mata pelajaran.');
    }

    /**
     * Remove a student from a course.
     */
    public function removeStudent(Course $course, User $student): RedirectResponse
    {
        $course->students()->detach($student->id);

        return back()->with('success', 'Siswa berhasil dikeluarkan dari mata pelajaran.');
    }
}
