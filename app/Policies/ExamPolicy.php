<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any exams.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the exam.
     */
    public function view(User $user, Exam $exam): bool
    {
        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can view their own exams
        if ($user->isTeacher() && $exam->created_by === $user->id) {
            return true;
        }

        // Student can view if enrolled in the course and exam is published
        if ($user->isStudent()) {
            return $exam->status !== Exam::STATUS_DRAFT 
                && $user->enrolledCourses()->where('courses.id', $exam->course_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create exams.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Determine whether the user can update the exam.
     */
    public function update(User $user, Exam $exam): bool
    {
        // Admin can update all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can update their own exams that are not completed
        return $user->isTeacher() 
            && $exam->created_by === $user->id 
            && !in_array($exam->status, [Exam::STATUS_COMPLETED, Exam::STATUS_ONGOING]);
    }

    /**
     * Determine whether the user can delete the exam.
     */
    public function delete(User $user, Exam $exam): bool
    {
        // Admin can delete all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can delete their own exams (any status)
        return $user->isTeacher() && $exam->created_by === $user->id;
    }

    /**
     * Determine whether the user can start the exam.
     */
    public function start(User $user, Exam $exam): bool
    {
        // Only students can start exams
        if (!$user->isStudent()) {
            return false;
        }

        // Check if enrolled in the course
        if (!$user->enrolledCourses()->where('courses.id', $exam->course_id)->exists()) {
            return false;
        }

        // Check if exam is active
        if (!$exam->isActive()) {
            return false;
        }

        // Check if already attempted
        $existingAttempt = $exam->attempts()
            ->where('user_id', $user->id)
            ->whereIn('status', ['in_progress', 'submitted', 'graded'])
            ->first();

        return !$existingAttempt;
    }

    /**
     * Determine whether the user can manage questions.
     */
    public function manageQuestions(User $user, Exam $exam): bool
    {
        return $this->update($user, $exam);
    }

    /**
     * Determine whether the user can view results.
     */
    public function viewResults(User $user, Exam $exam): bool
    {
        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can view results of their exams
        if ($user->isTeacher() && $exam->created_by === $user->id) {
            return true;
        }

        // Student can view their own result if show_score is enabled
        if ($user->isStudent() && ($exam->settings?->show_score ?? true)) {
            return $exam->attempts()->where('user_id', $user->id)->submitted()->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can monitor the exam.
     */
    public function monitor(User $user, Exam $exam): bool
    {
        // Admin can monitor all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can monitor their own exams
        return $user->isTeacher() && $exam->created_by === $user->id;
    }
}
