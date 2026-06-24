<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * Admin gets full access to all exam actions.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null; // Fall through to specific policy methods
    }

    /**
     * Check if the teacher is authorized for this exam.
     * A teacher is authorized if they created the exam OR if they are
     * assigned as the teacher of the exam's course.
     */
    protected function isTeacherOfExam(User $user, Exam $exam): bool
    {
        if (!$user->isTeacher()) {
            return false;
        }

        // Teacher created this exam
        if ((int) $exam->created_by === (int) $user->id) {
            return true;
        }

        // Teacher is assigned to the exam's course
        if ($exam->course && (int) $exam->course->teacher_id === (int) $user->id) {
            return true;
        }

        return false;
    }

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
        // Teacher can view exams they created or belong to their course
        if ($this->isTeacherOfExam($user, $exam)) {
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
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can update the exam.
     */
    public function update(User $user, Exam $exam): bool
    {
        // Teacher can update exams they own/are assigned to, if not completed
        return $this->isTeacherOfExam($user, $exam)
            && $exam->status !== Exam::STATUS_COMPLETED;
    }

    /**
     * Determine whether the user can delete the exam.
     */
    public function delete(User $user, Exam $exam): bool
    {
        // Teacher can delete exams they own or are assigned to
        return $this->isTeacherOfExam($user, $exam);
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

        // Check if has an in-progress attempt (can only have one at a time)
        $inProgressAttempt = $exam->attempts()
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->exists();

        if ($inProgressAttempt) {
            // Allow - they can continue their in-progress attempt
            return true;
        }

        // Count submitted attempts
        $submittedCount = $exam->attempts()
            ->where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->count();

        // Load settings if not already loaded to ensure we have max_attempts
        if (!$exam->relationLoaded('settings')) {
            $exam->load('settings');
        }

        // Get max attempts (0 = unlimited, null defaults to 1)
        $maxAttempts = $exam->settings->max_attempts ?? 1;

        // If unlimited (0) or hasn't reached max, allow
        if ($maxAttempts === 0 || $submittedCount < $maxAttempts) {
            return true;
        }

        return false;
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
        // Teacher can view results of exams they own/are assigned to
        if ($this->isTeacherOfExam($user, $exam)) {
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
        // Teacher can monitor exams they own or are assigned to
        return $this->isTeacherOfExam($user, $exam);
    }
}
