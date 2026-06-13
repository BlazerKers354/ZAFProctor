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
    private const MAX_BASE64_SNAPSHOT_LENGTH = 7 * 1024 * 1024;
    private const MAX_SNAPSHOT_BYTES = 5 * 1024 * 1024;

    private const ALLOWED_IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    private const MIME_EXTENSION_MAP = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    /**
     * Log a violation
     */
    public function logViolation(
        ExamAttempt $attempt,
        string $violationType,
        ?string $description = null,
        ?array $metadata = null,
        ?UploadedFile $snapshot = null,
        ?string $snapshotPath = null
    ): ProctoringLog {
        try {
            // Store snapshot from uploaded file if provided (legacy path)
            if ($snapshot && !$snapshotPath) {
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
            // Guard against excessive payloads before decoding.
            if (strlen($base64Data) > self::MAX_BASE64_SNAPSHOT_LENGTH) {
                Log::warning('Base64 snapshot too large for attempt ' . $attempt->id);
                return null;
            }

            $base64Payload = $base64Data;
            if (preg_match('/^data:image\/[a-zA-Z0-9.+-]+;base64,/', $base64Data) === 1) {
                $base64Payload = substr($base64Data, strpos($base64Data, ',') + 1);
            }

            $imageData = @base64_decode($base64Payload, true);
            
            if ($imageData === false || empty($imageData)) {
                Log::warning('Failed to decode base64 snapshot for attempt ' . $attempt->id);
                return null;
            }

            if (strlen($imageData) > self::MAX_SNAPSHOT_BYTES) {
                Log::warning('Decoded snapshot exceeds max size for attempt ' . $attempt->id);
                return null;
            }

            $imageInfo = @getimagesizefromstring($imageData);
            $detectedMimeType = is_array($imageInfo) ? ($imageInfo['mime'] ?? null) : null;

            if (!is_string($detectedMimeType) || !in_array($detectedMimeType, self::ALLOWED_IMAGE_MIME_TYPES, true)) {
                Log::warning('Rejected non-image snapshot payload for attempt ' . $attempt->id, [
                    'detected_mime' => $detectedMimeType,
                ]);
                return null;
            }

            $extension = self::MIME_EXTENSION_MAP[$detectedMimeType] ?? 'jpg';

            $filename = sprintf(
                'proctoring/%d/%d/%s.%s',
                $attempt->exam_id,
                $attempt->user_id,
                now()->format('Y-m-d_His_') . bin2hex(random_bytes(8)),
                $extension
            );

            // Store in private disk (not publicly accessible)
            $stored = Storage::disk('local')->put($filename, $imageData);
            
            if (!$stored) {
                Log::error('Failed to store snapshot file for attempt ' . $attempt->id);
                return null;
            }

            // Check disk usage and cleanup old snapshots if needed
            $this->cleanupOldestSnapshotsIfNeeded();

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
            if (!$file->isValid()) {
                Log::warning('Invalid uploaded snapshot for attempt ' . $attempt->id);
                return null;
            }

            $mimeType = $file->getMimeType();
            if (!is_string($mimeType) || !in_array($mimeType, self::ALLOWED_IMAGE_MIME_TYPES, true)) {
                Log::warning('Rejected uploaded snapshot with invalid mime type for attempt ' . $attempt->id, [
                    'mime_type' => $mimeType,
                ]);
                return null;
            }

            if ((int) $file->getSize() > self::MAX_SNAPSHOT_BYTES) {
                Log::warning('Uploaded snapshot exceeds max size for attempt ' . $attempt->id);
                return null;
            }

            $path = sprintf(
                'proctoring/%d/%d',
                $attempt->exam_id,
                $attempt->user_id
            );

            $extension = self::MIME_EXTENSION_MAP[$mimeType] ?? 'jpg';
            $filename = now()->format('Y-m-d_His_') . bin2hex(random_bytes(8)) . '.' . $extension;

            // Store in private disk (not publicly accessible)
            $storedPath = $file->storeAs($path, $filename, 'local');
            
            if (!$storedPath) {
                Log::error('Failed to store uploaded snapshot for attempt ' . $attempt->id);
                return null;
            }

            // Check disk usage and cleanup old snapshots if needed
            $this->cleanupOldestSnapshotsIfNeeded();
            
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

    /**
     * Check disk usage and delete oldest snapshots if storage is getting full.
     * Preserves violation log records — only deletes the snapshot image files.
     */
    public function cleanupOldestSnapshotsIfNeeded(): void
    {
        try {
            $storagePath = Storage::disk('local')->path('');
            $totalSpace = @disk_total_space($storagePath);
            $freeSpace = @disk_free_space($storagePath);

            if ($totalSpace === false || $freeSpace === false || $totalSpace <= 0) {
                return;
            }

            $usedPercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;
            $config = config('filesystems.snapshot_cleanup', []);
            $maxUsage = $config['max_usage_percent'] ?? 80;
            $targetUsage = $config['target_usage_percent'] ?? 70;
            $batchSize = $config['batch_size'] ?? 50;

            if ($usedPercent < $maxUsage) {
                return;
            }

            Log::info("Snapshot cleanup triggered: disk usage at " . round($usedPercent, 1) . "% (threshold: {$maxUsage}%)");

            $totalDeleted = 0;

            do {
                $oldestLogs = ProctoringLog::whereNotNull('snapshot_path')
                    ->orderBy('created_at', 'asc')
                    ->limit($batchSize)
                    ->get();

                if ($oldestLogs->isEmpty()) {
                    break;
                }

                foreach ($oldestLogs as $log) {
                    try {
                        Storage::disk('local')->delete($log->snapshot_path);
                    } catch (\Exception $e) {
                        // File may already be deleted, continue
                    }

                    $log->update(['snapshot_path' => null]);
                    $totalDeleted++;
                }

                // Re-check disk usage
                clearstatcache(true, $storagePath);
                $freeSpace = @disk_free_space($storagePath);
                if ($freeSpace === false) {
                    break;
                }
                $usedPercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;

            } while ($usedPercent > $targetUsage);

            if ($totalDeleted > 0) {
                Log::info("Snapshot cleanup complete: deleted {$totalDeleted} snapshot files. Disk usage now at " . round($usedPercent, 1) . "%");
            }
        } catch (\Exception $e) {
            Log::warning('Snapshot cleanup failed: ' . $e->getMessage());
        }
    }
}
