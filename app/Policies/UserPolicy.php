<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Users can view themselves
        if ((int) $user->id === (int) $model->id) {
            return true;
        }

        // Teacher can view students in their courses
        if ($user->isTeacher()) {
            return $model->isStudent() && 
                $model->enrolledCourses()
                    ->whereIn('courses.id', $user->taughtCourses()->pluck('id'))
                    ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the user.
     */
    public function update(User $user, User $model): bool
    {
        // Admin can update all
        if ($user->isAdmin()) {
            return true;
        }

        // Users can update themselves
        return (int) $user->id === (int) $model->id;
    }

    /**
     * Determine whether the user can delete the user.
     */
    public function delete(User $user, User $model): bool
    {
        // Only admin can delete
        if (!$user->isAdmin()) {
            return false;
        }

        // Cannot delete self
        return (int) $user->id !== (int) $model->id;
    }

    /**
     * Determine whether the user can change roles.
     */
    public function changeRole(User $user, User $model): bool
    {
        return $user->isAdmin() && (int) $user->id !== (int) $model->id;
    }
}
