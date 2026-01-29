<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    /**
     * Display a listing of classes.
     */
    public function index(): View
    {
        $classes = SchoolClass::withCount('students')
            ->with('homeroomTeacher')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new class.
     */
    public function create(): View
    {
        $teachers = User::byRole('teacher')->approved()->active()->orderBy('name')->get();
        return view('admin.classes.create', compact('teachers'));
    }

    /**
     * Store a newly created class.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'grade_level' => 'required|string|max:10',
            'description' => 'nullable|string|max:255',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $class = SchoolClass::create($validated);

        AuditLog::log(
            AuditLog::ACTION_CREATE,
            "Kelas {$class->name} dibuat",
            SchoolClass::class,
            $class->id
        );

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified class.
     */
    public function show(SchoolClass $class): View
    {
        $class->load(['homeroomTeacher', 'students']);
        $students = $class->students()->orderBy('name')->paginate(20);
        
        // Get students not assigned to any class
        $availableStudents = User::byRole('student')
            ->approved()
            ->active()
            ->whereNull('class_id')
            ->orderBy('name')
            ->get();

        return view('admin.classes.show', compact('class', 'students', 'availableStudents'));
    }

    /**
     * Show the form for editing the specified class.
     */
    public function edit(SchoolClass $class): View
    {
        $teachers = User::byRole('teacher')->approved()->active()->orderBy('name')->get();
        return view('admin.classes.edit', compact('class', 'teachers'));
    }

    /**
     * Update the specified class.
     */
    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'grade_level' => 'required|string|max:10',
            'description' => 'nullable|string|max:255',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $class->update($validated);

        AuditLog::log(
            AuditLog::ACTION_UPDATE,
            "Kelas {$class->name} diperbarui",
            SchoolClass::class,
            $class->id
        );

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified class.
     */
    public function destroy(SchoolClass $class): RedirectResponse
    {
        $className = $class->name;

        // Check if class has students
        if ($class->students()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kelas yang masih memiliki siswa.');
        }

        $class->delete();

        AuditLog::log(
            AuditLog::ACTION_DELETE,
            "Kelas {$className} dihapus",
            SchoolClass::class,
            null
        );

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    /**
     * Add students to class.
     */
    public function addStudents(Request $request, SchoolClass $class): RedirectResponse
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        User::whereIn('id', $validated['student_ids'])->update(['class_id' => $class->id]);

        AuditLog::log(
            AuditLog::ACTION_UPDATE,
            count($validated['student_ids']) . " siswa ditambahkan ke kelas {$class->name}",
            SchoolClass::class,
            $class->id
        );

        return back()->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    /**
     * Remove student from class.
     */
    public function removeStudent(SchoolClass $class, User $student): RedirectResponse
    {
        $student->update(['class_id' => null]);

        AuditLog::log(
            AuditLog::ACTION_UPDATE,
            "Siswa {$student->name} dihapus dari kelas {$class->name}",
            SchoolClass::class,
            $class->id
        );

        return back()->with('success', 'Siswa berhasil dihapus dari kelas.');
    }
}
