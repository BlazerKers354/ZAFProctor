<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Services\ProctoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProctoringController extends Controller
{
    public function __construct(
        protected ProctoringService $proctoringService
    ) {}

    /**
     * Log a violation from the client.
     */
    public function logViolation(Request $request, ExamAttempt $attempt): JsonResponse
    {
        // Validate ownership
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate that attempt is in progress
        if (!$attempt->isInProgress()) {
            return response()->json(['error' => 'Exam is not in progress'], 400);
        }

        $validated = $request->validate([
            'violation_type' => ['required', 'string', 'in:tab_switch,fullscreen_exit,camera_disabled,no_face_detected,multiple_faces,browser_refresh,copy_paste,right_click,keyboard_shortcut,window_blur,other'],
            'description' => ['nullable', 'string', 'max:500'],
            'metadata' => ['nullable', 'array'],
        ]);

        $log = $this->proctoringService->logViolation(
            $attempt,
            $validated['violation_type'],
            $validated['description'] ?? null,
            $validated['metadata'] ?? null
        );

        // Check if should auto-submit - use settings for threshold
        $settings = $attempt->exam->settings;
        $maxViolations = $settings?->auto_submit_threshold ?? $settings?->max_tab_switches ?? 5;
        $shouldAutoSubmit = $attempt->violation_count >= $maxViolations;

        return response()->json([
            'success' => true,
            'log_id' => $log->id,
            'violation_count' => $attempt->fresh()->violation_count,
            'should_auto_submit' => $shouldAutoSubmit,
            'warning_threshold' => $settings?->warning_threshold ?? 3,
        ]);
    }

    /**
     * Upload a snapshot from the client.
     */
    public function uploadSnapshot(Request $request, ExamAttempt $attempt): JsonResponse
    {
        // Validate ownership
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate that attempt is in progress
        if (!$attempt->isInProgress()) {
            return response()->json(['error' => 'Exam is not in progress'], 400);
        }

        $validated = $request->validate([
            'snapshot' => ['required', 'string'], // Base64 encoded image
            'violation_type' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        // Store the snapshot
        $snapshotPath = $this->proctoringService->storeSnapshotFromBase64(
            $attempt,
            $validated['snapshot']
        );

        // If there's a violation, log it with the snapshot
        if (!empty($validated['violation_type'])) {
            $log = $this->proctoringService->logViolation(
                $attempt,
                $validated['violation_type'],
                $validated['description'] ?? null,
                null,
                null // Snapshot already stored
            );

            // Update the log with snapshot path
            $log->update(['snapshot_path' => $snapshotPath]);
        }

        return response()->json([
            'success' => true,
            'snapshot_stored' => (bool) $snapshotPath,
        ]);
    }

    /**
     * Periodic heartbeat to track online status.
     */
    public function heartbeat(Request $request, ExamAttempt $attempt): JsonResponse
    {
        // Validate ownership
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Update camera status
        $attempt->update([
            'camera_enabled' => $request->boolean('camera_enabled', false),
        ]);

        // Check if time has expired
        if ($attempt->hasTimeExpired()) {
            return response()->json([
                'success' => true,
                'time_expired' => true,
                'should_submit' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'time_expired' => false,
            'remaining_time' => $attempt->remaining_time,
            'server_time' => now()->timestamp,
        ]);
    }

    /**
     * Get proctoring settings for the exam.
     */
    public function settings(ExamAttempt $attempt): JsonResponse
    {
        // Validate ownership
        if ($attempt->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $exam = $attempt->exam;
        $settings = $exam->settings ?? (object) \App\Models\ExamSetting::getDefaults();

        return response()->json([
            'require_camera' => $settings->webcam_enabled ?? true,
            'require_fullscreen' => $settings->browser_lock_enabled ?? true,
            'snapshot_interval' => $settings->snapshot_interval ?? 30,
            'detect_face' => $settings->detect_face ?? true,
            'detect_multiple_faces' => $settings->detect_multiple_faces ?? true,
            'detect_tab_switch' => $settings->detect_tab_switch ?? $settings->tab_switch_detection ?? true,
            'detect_fullscreen_exit' => $settings->detect_fullscreen_exit ?? true,
            'detect_copy_paste' => $settings->detect_copy_paste ?? true,
            'detect_right_click' => $settings->detect_right_click ?? true,
            'block_keyboard_shortcuts' => $settings->block_keyboard_shortcuts ?? true,
            'warning_threshold' => $settings->warning_threshold ?? 3,
            'auto_submit_threshold' => $settings->auto_submit_threshold ?? $settings->max_tab_switches ?? 5,
            'max_violations' => $settings->auto_submit_threshold ?? $settings->max_tab_switches ?? 5,
        ]);
    }
}
