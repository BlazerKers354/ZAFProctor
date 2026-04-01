<?php

namespace App\Http\Middleware;

use App\Models\ExamAttempt;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            try {
                // Auto-submit the exam using ExamService for proper score calculation
                $examService = app(\App\Services\ExamService::class);
                $examService->submitExam($attempt, true);

                return redirect()->route('student.exams.result', $attempt->id)
                    ->with('info', 'Waktu ujian telah habis. Jawaban Anda telah dikumpulkan secara otomatis.');
            } catch (\Exception $e) {
                Log::error('Auto-submit on time expiry failed: ' . $e->getMessage(), [
                    'attempt_id' => $attempt->id,
                    'user_id' => $attempt->user_id,
                ]);

                // Still redirect to result page even if auto-submit failed
                return redirect()->route('student.exams.result', $attempt->id)
                    ->with('warning', 'Waktu ujian telah habis. Jawaban Anda sedang diproses.');
            }
        }

        // Attach attempt to request for use in controller
        $request->merge(['exam_attempt' => $attempt]);

        return $next($request);
    }
}
