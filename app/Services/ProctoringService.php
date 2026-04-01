<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ExamAttempt;
use App\Models\ProctoringLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProctoringService
{
    /**
     * Log a violation
     */
    public function logViolation(
        ExamAttempt $attempt,
        string $violationType,
        ?string $description = null,
        ?array $metadata = null,
        ?UploadedFile $snapshot = null
    ): ProctoringLog {
        try {
            $snapshotPath = null;

            // Store snapshot if provided
            if ($snapshot) {
                $snapshotPath = $this->storeSnapshot($attempt, $snapshot);
            }

            // Determine severity
            $severity = ProctoringLog::getSeverityForType($violationType);

            // Create proctoring log
            $log = ProctoringLog::create([
                'attempt_id' => $attempt->id,
                'user_id' => $attempt->user_id,
                'violation_type' => $violationType,
                'description' => $description,
                'snapshot_path' => $snapshotPath,
                'metadata' => $metadata,
                'severity' => $severity,
            ]);

            // Increment violation count on attempt
            $this->incrementViolationCount($attempt, $violationType);

            // Log audit (wrapped separately to not fail main operation)
            try {
                AuditLog::log(
                    AuditLog::ACTION_VIOLATION,
                    "Proctoring violation: {$violationType}",
                    ProctoringLog::class,
                    $log->id
                );
            } catch (\Exception $e) {
                Log::warning('Failed to create audit log for violation: ' . $e->getMessage());
            }

            return $log;
        } catch (\Exception $e) {
            Log::error('Failed to log violation: ' . $e->getMessage(), [
                'attempt_id' => $attempt->id,
                'violation_type' => $violationType,
            ]);
            throw $e;
        }
    }

    /**
     * Store a snapshot from base64 data
     */
    public function storeSnapshotFromBase64(ExamAttempt $attempt, string $base64Data): ?string
    {
        try {
            // Validate base64 data size (max 10MB)
            if (strlen($base64Data) > 10 * 1024 * 1024) {
                Log::warning('Base64 snapshot too large for attempt ' . $attempt->id);
                return null;
            }

            // Remove data URL prefix if present
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
                $extension = $matches[1];
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                
                // Validate extension
                $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
                if (!in_array(strtolower($extension), $allowedExtensions)) {
                    Log::warning('Invalid image extension in base64 for attempt ' . $attempt->id);
                    $extension = 'jpg';
                }
            } else {
                $extension = 'jpg';
            }

            $imageData = @base64_decode($base64Data, true);
            
            if ($imageData === false || empty($imageData)) {
                Log::warning('Failed to decode base64 snapshot for attempt ' . $attempt->id);
                return null;
            }

            $filename = sprintf(
                'proctoring/%d/%d/%s.%s',
                $attempt->exam_id,
                $attempt->user_id,
                now()->format('Y-m-d_His_') . uniqid(),
                $extension
            );

            // Store in private disk (not publicly accessible)
            $stored = Storage::disk('local')->put($filename, $imageData);
            
            if (!$stored) {
                Log::error('Failed to store snapshot file for attempt ' . $attempt->id);
                return null;
            }

            return $filename;
        } catch (\Exception $e) {
            Log::error('Snapshot storage error: ' . $e->getMessage(), [
                'attempt_id' => $attempt->id,
            ]);
            return null;
        }
    }

    /**
     * Store an uploaded snapshot
     */
    protected function storeSnapshot(ExamAttempt $attempt, UploadedFile $file): ?string
    {
        try {
            $path = sprintf(
                'proctoring/%d/%d',
                $attempt->exam_id,
                $attempt->user_id
            );

            // Store in private disk (not publicly accessible)
            $storedPath = $file->store($path, 'local');
            
            if (!$storedPath) {
                Log::error('Failed to store uploaded snapshot for attempt ' . $attempt->id);
                return null;
            }
            
            return $storedPath;
        } catch (\Exception $e) {
            Log::error('Failed to store uploaded snapshot: ' . $e->getMessage(), [
                'attempt_id' => $attempt->id,
            ]);
            return null;
        }
    }

    /**
     * Increment violation count based on type using atomic update
     */
    protected function incrementViolationCount(ExamAttempt $attempt, string $violationType): void
    {
        try {
            // Use atomic increment to prevent race condition
            $updateData = ['violation_count' => DB::raw('violation_count + 1')];

            if ($violationType === ProctoringLog::TYPE_TAB_SWITCH) {
                $updateData['tab_switch_count'] = DB::raw('tab_switch_count + 1');
            } elseif ($violationType === ProctoringLog::TYPE_FULLSCREEN_EXIT) {
                $updateData['fullscreen_exit_count'] = DB::raw('fullscreen_exit_count + 1');
            }

            ExamAttempt::where('id', $attempt->id)->update($updateData);
            $attempt->refresh();
        } catch (\Exception $e) {
            Log::error('Failed to increment violation count: ' . $e->getMessage(), [
                'attempt_id' => $attempt->id,
                'violation_type' => $violationType,
            ]);
        }
    }

    /**
     * Get violation summary for an attempt
     */
    public function getViolationSummary(ExamAttempt $attempt): array
    {
        try {
            $logs = $attempt->proctoringLogs;

            return [
                'total' => $logs->count(),
                'by_type' => $logs->groupBy('violation_type')
                    ->map(fn($group) => $group->count())
                    ->toArray(),
                'by_severity' => [
                    'high' => $logs->where('severity', ProctoringLog::SEVERITY_HIGH)->count(),
                    'medium' => $logs->where('severity', ProctoringLog::SEVERITY_MEDIUM)->count(),
                    'low' => $logs->where('severity', ProctoringLog::SEVERITY_LOW)->count(),
                ],
                'reviewed' => $logs->where('is_reviewed', true)->count(),
                'pending_review' => $logs->where('is_reviewed', false)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get violation summary: ' . $e->getMessage(), [
                'attempt_id' => $attempt->id,
            ]);
            
            return [
                'total' => 0,
                'by_type' => [],
                'by_severity' => ['high' => 0, 'medium' => 0, 'low' => 0],
                'reviewed' => 0,
                'pending_review' => 0,
            ];
        }
    }

    /**
     * Mark multiple logs as reviewed
     */
    public function markAsReviewed(array $logIds, int $reviewerId, ?string $notes = null): int
    {
        try {
            return ProctoringLog::whereIn('id', $logIds)
                ->update([
                    'is_reviewed' => true,
                    'reviewed_by' => $reviewerId,
                    'reviewed_at' => now(),
                    'review_notes' => $notes,
                ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark logs as reviewed: ' . $e->getMessage(), [
                'log_ids' => $logIds,
                'reviewer_id' => $reviewerId,
            ]);
            return 0;
        }
    }

    /**
     * Get snapshots for an attempt
     */
    public function getSnapshots(ExamAttempt $attempt): array
    {
        try {
            return $attempt->proctoringLogs()
                ->whereNotNull('snapshot_path')
                ->orderBy('created_at')
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'url' => route('proctoring.snapshot.view', $log->id),
                        'violation_type' => $log->violation_type,
                        'violation_label' => $log->violation_label,
                        'severity' => $log->severity,
                        'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to get snapshots: ' . $e->getMessage(), [
                'attempt_id' => $attempt->id,
            ]);
            return [];
        }
    }

    /**
     * Check if camera was consistently enabled
     */
    public function checkCameraConsistency(ExamAttempt $attempt): array
    {
        try {
            $cameraDisabledLogs = $attempt->proctoringLogs()
                ->where('violation_type', ProctoringLog::TYPE_CAMERA_DISABLED)
                ->count();

            $totalSnapshots = $attempt->proctoringLogs()
                ->whereNotNull('snapshot_path')
                ->count();

            $duration = $attempt->started_at && $attempt->submitted_at
                ? $attempt->started_at->diffInMinutes($attempt->submitted_at)
                : 0;

            $expectedSnapshots = $duration > 0 
                ? floor($duration * 60 / ($attempt->exam->settings?->snapshot_interval ?? 30))
                : 0;

            return [
                'camera_disabled_count' => $cameraDisabledLogs,
                'total_snapshots' => $totalSnapshots,
                'expected_snapshots' => $expectedSnapshots,
                'snapshot_coverage' => $expectedSnapshots > 0 
                    ? round(($totalSnapshots / $expectedSnapshots) * 100, 2) 
                    : 0,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to check camera consistency: ' . $e->getMessage(), [
                'attempt_id' => $attempt->id,
            ]);
            
            return [
                'camera_disabled_count' => 0,
                'total_snapshots' => 0,
                'expected_snapshots' => 0,
                'snapshot_coverage' => 0,
            ];
        }
    }
}
