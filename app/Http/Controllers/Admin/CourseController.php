<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request): View
    {
        try {
            $query = Course::with('teacher')->withCount(['students', 'exams']);

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
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

            $courses = $query->latest()->paginate(15)->withQueryString();
            $teachers = User::byRole(Role::TEACHER)->get();

            return view('admin.courses.index', compact('courses', 'teachers'));
        } catch (\Exception $e) {
            Log::error('Failed to load courses list: ' . $e->getMessage());
            
            return view('admin.courses.index', [
                'courses' => collect(),
                'teachers' => collect(),
            ])->with('error', 'Gagal memuat daftar mata pelajaran.');
        }
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): View
    {
        $teachers = User::byRole(Role::TEACHER)->active()->get();
        $students = User::byRole(Role::STUDENT)->active()->get();
        return view('admin.courses.create', compact('teachers', 'students'));
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'code' => ['required', 'string', 'max:20', 'unique:courses'],
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'teacher_id' => ['nullable', 'exists:users,id'],
                'students' => ['nullable', 'array'],
                'students.*' => ['exists:users,id'],
                'is_active' => ['boolean'],
            ]);

            $validated['is_active'] = $request->boolean('is_active', true);
            
            $students = $request->input('students', []);
            unset($validated['students']);

            DB::transaction(function () use ($validated, $students, &$course) {
                $course = Course::create($validated);
                
                // Attach students if any
                if (!empty($students)) {
                    $course->students()->attach($students);
                }
            });

            return redirect()->route('admin.courses.index')
                ->with('success', 'Mata pelajaran berhasil dibuat.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to create course: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Gagal membuat mata pelajaran. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course): View
    {
        try {
            $course->load(['teacher', 'students', 'exams.creator']);

            return view('admin.courses.show', compact('course'));
        } catch (\Exception $e) {
            Log::error('Failed to load course details: ' . $e->getMessage(), ['course_id' => $course->id]);
            
            return view('admin.courses.show', compact('course'))
                ->with('error', 'Beberapa data tidak dapat dimuat.');
        }
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
        try {
            $validated = $request->validate([
                'code' => ['required', 'string', 'max:20', 'unique:courses,code,' . $course->id],
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'teacher_id' => ['nullable', 'exists:users,id'],
                'is_active' => ['boolean'],
            ]);

            $validated['is_active'] = $request->boolean('is_active', true);

            $course->update($validated);

            return redirect()->route('admin.courses.index')
                ->with('success', 'Mata pelajaran berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update course: ' . $e->getMessage(), ['course_id' => $course->id]);
            
            return back()->withErrors(['error' => 'Gagal memperbarui mata pelajaran.'])->withInput();
        }
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course): RedirectResponse
    {
        try {
            $course->delete();

            return redirect()->route('admin.courses.index')
                ->with('success', 'Mata pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Failed to delete course: ' . $e->getMessage(), ['course_id' => $course->id]);
            
            return back()->with('error', 'Gagal menghapus mata pelajaran.');
        }
    }

    /**
     * Add students to a course.
     */
    public function addStudents(Request $request, Course $course): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'student_ids' => ['required', 'array'],
                'student_ids.*' => ['exists:users,id'],
            ]);

            $course->students()->syncWithoutDetaching($validated['student_ids']);

            return back()->with('success', 'Siswa berhasil ditambahkan ke mata pelajaran.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to add students to course: ' . $e->getMessage(), ['course_id' => $course->id]);
            
            return back()->with('error', 'Gagal menambahkan siswa ke mata pelajaran.');
        }
    }

    /**
     * Remove a student from a course.
     */
    public function removeStudent(Course $course, User $student): RedirectResponse
    {
        try {
            $course->students()->detach($student->id);

            return back()->with('success', 'Siswa berhasil dikeluarkan dari mata pelajaran.');
        } catch (\Exception $e) {
            Log::error('Failed to remove student from course: ' . $e->getMessage(), [
                'course_id' => $course->id,
                'student_id' => $student->id,
            ]);
            
            return back()->with('error', 'Gagal mengeluarkan siswa dari mata pelajaran.');
        }
    }
}
