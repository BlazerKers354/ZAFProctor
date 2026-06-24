<?php

namespace App\Policies;

use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamAttemptPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can interact with an active attempt
     * (save answer, submit, sync time, and proctoring actions).
     */
    public function interact(User $user, ExamAttempt $attempt): bool
    {
        return $user->isStudent()
            && (int) $attempt->user_id === (int) $user->id
            && $attempt->isInProgress();
    }

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
        if ((int) $attempt->user_id === (int) $user->id) {
            return true;
        }

        // Teacher can view attempts for their exams
        return $user->isTeacher() && (int) $attempt->exam->created_by === (int) $user->id;
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
        return $user->isTeacher() && (int) $attempt->exam->created_by === (int) $user->id;
    }

    /**
     * Determine whether the user can review proctoring logs.
     */
    public function reviewProctoring(User $user, ExamAttempt $attempt): bool
    {
        return $this->grade($user, $attempt);
    }
}
