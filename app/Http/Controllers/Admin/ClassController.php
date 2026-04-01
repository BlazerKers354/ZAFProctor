<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ClassController extends Controller
{
    /**
     * Display a listing of classes.
     */
    public function index(): View
    {
        try {
            $classes = SchoolClass::withCount('students')
                ->with('homeroomTeacher')
                ->orderBy('grade_level')
                ->orderBy('name')
                ->paginate(15);

            return view('admin.classes.index', compact('classes'));
        } catch (\Exception $e) {
            Log::error('Failed to load classes list: ' . $e->getMessage());
            
            return view('admin.classes.index', ['classes' => collect()])
                ->with('error', 'Gagal memuat daftar kelas.');
        }
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50',
                'grade_level' => 'required|string|max:10',
                'description' => 'nullable|string|max:255',
                'homeroom_teacher_id' => 'nullable|exists:users,id',
                'is_active' => 'boolean',
            ]);

            $validated['is_active'] = $request->boolean('is_active', true);

            $class = SchoolClass::create($validated);

            try {
                AuditLog::log(
                    AuditLog::ACTION_CREATE,
                    "Kelas {$class->name} dibuat",
                    SchoolClass::class,
                    $class->id
                );
            } catch (\Exception $e) {
                Log::warning('Failed to create audit log for class creation: ' . $e->getMessage());
            }

            return redirect()->route('admin.classes.index')
                ->with('success', 'Kelas berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to create class: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Gagal membuat kelas. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Display the specified class.
     */
    public function show(SchoolClass $class): View
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Failed to load class details: ' . $e->getMessage(), ['class_id' => $class->id]);
            
            return view('admin.classes.show', [
                'class' => $class,
                'students' => collect(),
                'availableStudents' => collect(),
            ])->with('error', 'Beberapa data tidak dapat dimuat.');
        }
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50',
                'grade_level' => 'required|string|max:10',
                'description' => 'nullable|string|max:255',
                'homeroom_teacher_id' => 'nullable|exists:users,id',
                'is_active' => 'boolean',
            ]);

            $validated['is_active'] = $request->boolean('is_active', true);

            $class->update($validated);

            try {
                AuditLog::log(
                    AuditLog::ACTION_UPDATE,
                    "Kelas {$class->name} diperbarui",
                    SchoolClass::class,
                    $class->id
                );
            } catch (\Exception $e) {
                Log::warning('Failed to create audit log for class update: ' . $e->getMessage());
            }

            return redirect()->route('admin.classes.index')
                ->with('success', 'Kelas berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update class: ' . $e->getMessage(), ['class_id' => $class->id]);
            
            return back()->withErrors(['error' => 'Gagal memperbarui kelas.'])->withInput();
        }
    }

    /**
     * Remove the specified class.
     */
    public function destroy(SchoolClass $class): RedirectResponse
    {
        try {
            $className = $class->name;

            // Check if class has students
            if ($class->students()->count() > 0) {
                return back()->with('error', 'Tidak dapat menghapus kelas yang masih memiliki siswa.');
            }

            $class->delete();

            try {
                AuditLog::log(
                    AuditLog::ACTION_DELETE,
                    "Kelas {$className} dihapus",
                    SchoolClass::class,
                    null
                );
            } catch (\Exception $e) {
                Log::warning('Failed to create audit log for class deletion: ' . $e->getMessage());
            }

            return redirect()->route('admin.classes.index')
                ->with('success', 'Kelas berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Failed to delete class: ' . $e->getMessage(), ['class_id' => $class->id]);
            
            return back()->with('error', 'Gagal menghapus kelas.');
        }
    }

    /**
     * Add students to class.
     */
    public function addStudents(Request $request, SchoolClass $class): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'student_ids' => 'required|array',
                'student_ids.*' => 'exists:users,id',
            ]);

            DB::transaction(function () use ($validated, $class) {
                $updated = User::whereIn('id', $validated['student_ids'])->update(['class_id' => $class->id]);
                
                if ($updated > 0) {
                    try {
                        AuditLog::log(
                            AuditLog::ACTION_UPDATE,
                            count($validated['student_ids']) . " siswa ditambahkan ke kelas {$class->name}",
                            SchoolClass::class,
                            $class->id
                        );
                    } catch (\Exception $e) {
                        Log::warning('Failed to create audit log: ' . $e->getMessage());
                    }
                }
            });

            return back()->with('success', 'Siswa berhasil ditambahkan ke kelas.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to add students to class: ' . $e->getMessage(), ['class_id' => $class->id]);
            
            return back()->with('error', 'Gagal menambahkan siswa ke kelas.');
        }
    }

    /**
     * Remove student from class.
     */
    public function removeStudent(SchoolClass $class, User $student): RedirectResponse
    {
        try {
            $student->update(['class_id' => null]);

            try {
                AuditLog::log(
                    AuditLog::ACTION_UPDATE,
                    "Siswa {$student->name} dihapus dari kelas {$class->name}",
                    SchoolClass::class,
                    $class->id
                );
            } catch (\Exception $e) {
                Log::warning('Failed to create audit log: ' . $e->getMessage());
            }

            return back()->with('success', 'Siswa berhasil dihapus dari kelas.');
        } catch (\Exception $e) {
            Log::error('Failed to remove student from class: ' . $e->getMessage(), [
                'class_id' => $class->id,
                'student_id' => $student->id,
            ]);
            
            return back()->with('error', 'Gagal menghapus siswa dari kelas.');
        }
    }
}
