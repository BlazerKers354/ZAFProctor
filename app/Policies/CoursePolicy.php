<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any courses.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the course.
     */
    public function view(User $user, Course $course): bool
    {
        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can view their own courses
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return true;
        }

        // Student can view if enrolled
        if ($user->isStudent()) {
            return $course->students()->where('users.id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create courses.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the course.
     */
    public function update(User $user, Course $course): bool
    {
        // Admin can update all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can update their own courses
        return $user->isTeacher() && $course->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can delete the course.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can manage students in the course.
     */
    public function manageStudents(User $user, Course $course): bool
    {
        // Admin can manage all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can manage their own courses
        return $user->isTeacher() && $course->teacher_id === $user->id;
    }
}
