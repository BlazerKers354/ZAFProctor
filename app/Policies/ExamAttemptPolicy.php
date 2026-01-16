<?php

namespace App\Policies;

use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamAttemptPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the attempt.
     */
    public function view(User $user, ExamAttempt $attempt): bool
    {
        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Owner can view their attempt
        if ($attempt->user_id === $user->id) {
            return true;
        }

        // Teacher can view attempts for their exams
        return $user->isTeacher() && $attempt->exam->created_by === $user->id;
    }

    /**
     * Determine whether the user can grade the attempt.
     */
    public function grade(User $user, ExamAttempt $attempt): bool
    {
        // Admin can grade all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can grade attempts for their exams
        return $user->isTeacher() && $attempt->exam->created_by === $user->id;
    }

    /**
     * Determine whether the user can review proctoring logs.
     */
    public function reviewProctoring(User $user, ExamAttempt $attempt): bool
    {
        return $this->grade($user, $attempt);
    }
}
