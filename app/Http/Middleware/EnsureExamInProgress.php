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
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ujian sudah selesai atau belum dimulai.',
                    'attempt_submitted' => $attempt->isSubmitted(),
                    'redirect' => $attempt->isSubmitted()
                        ? route('student.exams.result', $attempt->id)
                        : route('student.exams.index'),
                ], 409);
            }

            if ($attempt->isSubmitted()) {
                return redirect()->route('student.exams.result', $attempt->id)
                    ->with('info', 'Ujian sudah dikumpulkan. Berikut halaman hasil ujian Anda.');
            }

            return redirect()->route('student.exams.index')
                ->with('error', 'Ujian sudah selesai atau belum dimulai.');
        }

        // Check if time has expired
        if ($attempt->hasTimeExpired()) {
            try {
                // Auto-submit the exam using ExamService for proper score calculation
                $examService = app(\App\Services\ExamService::class);
                $examService->submitExam($attempt, true);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Waktu ujian telah habis. Jawaban Anda telah dikumpulkan secara otomatis.',
                        'attempt_submitted' => true,
                        'should_submit' => true,
                        'redirect' => route('student.exams.result', $attempt->id),
                    ], 409);
                }

                return redirect()->route('student.exams.result', $attempt->id)
                    ->with('info', 'Waktu ujian telah habis. Jawaban Anda telah dikumpulkan secara otomatis.');
            } catch (\Exception $e) {
                Log::error('Auto-submit on time expiry failed: ' . $e->getMessage(), [
                    'attempt_id' => $attempt->id,
                    'user_id' => $attempt->user_id,
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Waktu ujian telah habis. Jawaban Anda sedang diproses.',
                        'attempt_submitted' => true,
                        'should_submit' => true,
                        'redirect' => route('student.exams.result', $attempt->id),
                    ], 409);
                }

                // Still redirect to result page even if auto-submit failed
                return redirect()->route('student.exams.result', $attempt->id)
                    ->with('warning', 'Waktu ujian telah habis. Jawaban Anda sedang diproses.');
            }
        }

        // Check if violations exceeded the limit (server-side enforcement)
        if ($attempt->hasExceededViolations()) {
            try {
                $examService = app(\App\Services\ExamService::class);
                $examService->submitExam($attempt, true);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ujian telah dikumpulkan secara otomatis karena jumlah pelanggaran mencapai batas yang ditentukan.',
                        'attempt_submitted' => true,
                        'should_submit' => true,
                        'redirect' => route('student.exams.result', $attempt->id),
                    ], 409);
                }

                return redirect()->route('student.exams.result', $attempt->id)
                    ->with('info', 'Ujian telah dikumpulkan secara otomatis karena jumlah pelanggaran mencapai batas yang ditentukan.');
            } catch (\Exception $e) {
                Log::error('Auto-submit on violation limit failed: ' . $e->getMessage(), [
                    'attempt_id' => $attempt->id,
                    'user_id' => $attempt->user_id,
                    'violation_count' => $attempt->violation_count,
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ujian telah dikumpulkan karena pelanggaran mencapai batas.',
                        'attempt_submitted' => true,
                        'should_submit' => true,
                        'redirect' => route('student.exams.result', $attempt->id),
                    ], 409);
                }

                return redirect()->route('student.exams.result', $attempt->id)
                    ->with('warning', 'Ujian telah dikumpulkan karena pelanggaran mencapai batas.');
            }
        }

        // Attach attempt to request for use in controller
        $request->merge(['exam_attempt' => $attempt]);

        return $next($request);
    }
}
