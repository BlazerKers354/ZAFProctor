<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Services\ProctoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProctoringController extends Controller
{
    private const MAX_BASE64_SNAPSHOT_LENGTH = 7 * 1024 * 1024;

    public function __construct(
        protected ProctoringService $proctoringService
    ) {}

    /**
     * Log a violation from the client.
     */
    public function logViolation(Request $request, ExamAttempt $attempt): JsonResponse
    {
        $this->authorize('interact', $attempt);

        $settings = $attempt->exam->settings;

        // Map violation types to their required settings
        $featureGuards = [
            'tab_switch' => $settings?->tab_switch_detection ?? true,
            'window_blur' => $settings?->tab_switch_detection ?? true,
            'fullscreen_exit' => $settings?->browser_lock_enabled ?? true,
            'camera_disabled' => $settings?->webcam_enabled ?? true,
            'no_face_detected' => $settings?->webcam_enabled ?? true,
            'multiple_faces' => $settings?->webcam_enabled ?? true,
            'copy_paste' => $settings?->block_keyboard_shortcuts ?? true,
            'right_click' => $settings?->block_keyboard_shortcuts ?? true,
            'keyboard_shortcut' => $settings?->block_keyboard_shortcuts ?? true,
        ];

        $validated = $request->validate([
            'violation_type' => ['required', 'string', Rule::in($this->allowedViolationTypes())],
            'description' => ['nullable', 'string', 'max:1000'],
            'metadata' => ['nullable', 'array', 'max:20'],
            'snapshot' => ['nullable', 'string', 'max:' . self::MAX_BASE64_SNAPSHOT_LENGTH],
        ]);

        // Short-circuit if the monitoring feature for this violation type is disabled
        $violationType = $validated['violation_type'];
        if (isset($featureGuards[$violationType]) && !$featureGuards[$violationType]) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur pemantauan terkait tidak aktif untuk ujian ini.',
            ], 422);
        }

        if (array_key_exists('metadata', $validated)) {
            $encodedMetadata = json_encode($validated['metadata']);

            if ($encodedMetadata === false || strlen($encodedMetadata) > 4096) {
                return response()->json([
                    'error' => 'Metadata terlalu besar atau tidak valid.',
                ], 422);
            }
        }

        // Store snapshot alongside the violation if provided
        $snapshotPath = null;
        if (!empty($validated['snapshot'])) {
            $snapshotPath = $this->proctoringService->storeSnapshotFromBase64(
                $attempt,
                $validated['snapshot']
            );
        }

        $log = $this->proctoringService->logViolation(
            $attempt,
            $validated['violation_type'],
            $validated['description'] ?? null,
            $validated['metadata'] ?? null,
            null,
            $snapshotPath
        );

        // Check if should auto-submit - use settings for threshold
        $settings = $attempt->exam->settings;
        $maxViolations = $this->resolveMaxViolations($settings);
        $freshAttempt = $attempt->fresh();
        $shouldAutoSubmit = $maxViolations > 0 && $freshAttempt->violation_count >= $maxViolations;

        return response()->json([
            'success' => true,
            'log_id' => $log->id,
            'violation_count' => $freshAttempt->violation_count,
            'should_auto_submit' => $shouldAutoSubmit,
            'max_violations' => $maxViolations,
        ]);
    }

    /**
     * Upload a snapshot from the client.
     */
    public function uploadSnapshot(Request $request, ExamAttempt $attempt): JsonResponse
    {
        $this->authorize('interact', $attempt);

        // Short-circuit if webcam is not enabled for this exam
        if (!($attempt->exam->settings?->webcam_enabled ?? true)) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur webcam tidak aktif untuk ujian ini.',
            ], 422);
        }

        $validated = $request->validate([
            'snapshot' => [
                'required',
                'string',
                'max:' . self::MAX_BASE64_SNAPSHOT_LENGTH,
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (!is_string($value)) {
                        $fail('Format snapshot tidak valid.');
                        return;
                    }

                    $payload = $value;
                    if (preg_match('/^data:image\/[a-zA-Z0-9.+-]+;base64,/', $value) === 1) {
                        $payload = substr($value, strpos($value, ',') + 1);
                    }

                    $payload = preg_replace('/\s+/', '', $payload ?? '');
                    if (!is_string($payload) || $payload === '') {
                        $fail('Format snapshot tidak valid.');
                        return;
                    }

                    if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $payload)) {
                        $fail('Format snapshot tidak valid.');
                    }
                },
            ],
            'violation_type' => ['nullable', 'string', Rule::in($this->allowedViolationTypes())],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        // Store the snapshot
        $snapshotPath = $this->proctoringService->storeSnapshotFromBase64(
            $attempt,
            $validated['snapshot']
        );

        if (!$snapshotPath) {
            return response()->json([
                'success' => false,
                'message' => 'Snapshot tidak valid atau ukuran file terlalu besar.',
            ], 422);
        }

        // Snapshot uploads are evidence-only. Violation counting must go through
        // logViolation so one client event cannot be counted twice.
        $settings = $attempt->exam->settings;
        $maxViolations = $this->resolveMaxViolations($settings);
        $freshAttempt = $attempt->fresh();

        return response()->json([
            'success' => true,
            'snapshot_stored' => (bool) $snapshotPath,
            'should_auto_submit' => false,
            'violation_count' => $freshAttempt->violation_count,
            'max_violations' => $maxViolations,
        ]);
    }

    /**
     * Periodic heartbeat to track online status.
     */
    public function heartbeat(Request $request, ExamAttempt $attempt): JsonResponse
    {
        $this->authorize('interact', $attempt);

        $validated = $request->validate([
            'camera_enabled' => ['required', 'boolean'],
        ]);

        // Update camera status
        $attempt->update([
            'camera_enabled' => (bool) $validated['camera_enabled'],
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
        $this->authorize('interact', $attempt);

        $exam = $attempt->exam;
        $settings = $exam->settings ?? (object) \App\Models\ExamSetting::getDefaults();
        $maxViolations = $this->resolveMaxViolations($settings);

        return response()->json([
            'require_camera' => $settings->webcam_enabled ?? true,
            'require_fullscreen' => $settings->browser_lock_enabled ?? true,
            'snapshot_interval' => $settings->snapshot_interval ?? 30,
            'detect_face' => $settings->webcam_enabled ?? true,
            'detect_multiple_faces' => $settings->webcam_enabled ?? true,
            'detect_tab_switch' => $settings->tab_switch_detection ?? true,
            'detect_fullscreen_exit' => $settings->browser_lock_enabled ?? true,
            'detect_copy_paste' => $settings->block_keyboard_shortcuts ?? true,
            'detect_right_click' => $settings->block_keyboard_shortcuts ?? true,
            'block_keyboard_shortcuts' => $settings->block_keyboard_shortcuts ?? true,
            'auto_submit_threshold' => $maxViolations,
            'max_violations' => $maxViolations,
        ]);
    }

    /**
     * Resolve max violations from settings.
     * Returns 0 when unlimited (for backward-compatible JSON response format).
     */
    protected function resolveMaxViolations($settings): int
    {
        return $settings?->resolveViolationLimit() ?? 0;
    }

    /**
     * Allowed violation types accepted by server-side validation.
     */
    protected function allowedViolationTypes(): array
    {
        return [
            'tab_switch',
            'fullscreen_exit',
            'camera_disabled',
            'no_face_detected',
            'multiple_faces',
            'browser_refresh',
            'copy_paste',
            'right_click',
            'keyboard_shortcut',
            'window_blur',
            'devtools',
            'tampering',
            'other',
        ];
    }
}
