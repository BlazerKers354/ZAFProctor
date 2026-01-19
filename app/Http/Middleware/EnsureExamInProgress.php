<?php

namespace App\Http\Middleware;

use App\Models\ExamAttempt;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureExamInProgress
{
    /**
     * Handle an incoming request.
     * Ensures that the exam attempt is still in progress.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $attemptParam = $request->route('attempt');
        
        if (!$attemptParam) {
            abort(404, 'Exam attempt not found.');
        }

        // Handle both route model binding (ExamAttempt instance) and raw ID
        if ($attemptParam instanceof ExamAttempt) {
            $attempt = $attemptParam;
        } else {
            $attempt = ExamAttempt::find($attemptParam);
        }

        if (!$attempt) {
            abort(404, 'Exam attempt not found.');
        }

        // Check ownership
        if ($attempt->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to exam attempt.');
        }

        // Check if attempt is in progress
        if (!$attempt->isInProgress()) {
            return redirect()->route('student.exams.index')
                ->with('error', 'Ujian sudah selesai atau belum dimulai.');
        }

        // Check if time has expired
        if ($attempt->hasTimeExpired()) {
            // Auto-submit the exam
            $attempt->update([
                'status' => ExamAttempt::STATUS_SUBMITTED,
                'submitted_at' => now(),
                'is_auto_submitted' => true,
            ]);

            return redirect()->route('student.exams.result', $attempt->id)
                ->with('info', 'Waktu ujian telah habis. Jawaban Anda telah dikumpulkan secara otomatis.');
        }

        // Attach attempt to request for use in controller
        $request->merge(['exam_attempt' => $attempt]);

        return $next($request);
    }
}
