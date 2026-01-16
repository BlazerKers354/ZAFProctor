<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ExamAttempt;
use App\Models\ProctoringLog;
use Illuminate\Http\UploadedFile;
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

        // Log audit
        AuditLog::log(
            AuditLog::ACTION_VIOLATION,
            "Proctoring violation: {$violationType}",
            ProctoringLog::class,
            $log->id
        );

        return $log;
    }

    /**
     * Store a snapshot from base64 data
     */
    public function storeSnapshotFromBase64(ExamAttempt $attempt, string $base64Data): ?string
    {
        // Remove data URL prefix if present
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
            $extension = $matches[1];
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
        } else {
            $extension = 'jpg';
        }

        $imageData = base64_decode($base64Data);
        
        if ($imageData === false) {
            return null;
        }

        $filename = sprintf(
            'proctoring/%d/%d/%s.%s',
            $attempt->exam_id,
            $attempt->user_id,
            now()->format('Y-m-d_His_') . uniqid(),
            $extension
        );

        Storage::disk('local')->put($filename, $imageData);

        return $filename;
    }

    /**
     * Store an uploaded snapshot
     */
    protected function storeSnapshot(ExamAttempt $attempt, UploadedFile $file): string
    {
        $path = sprintf(
            'proctoring/%d/%d',
            $attempt->exam_id,
            $attempt->user_id
        );

        return $file->store($path, 'local');
    }

    /**
     * Increment violation count based on type
     */
    protected function incrementViolationCount(ExamAttempt $attempt, string $violationType): void
    {
        $attempt->violation_count++;

        if ($violationType === ProctoringLog::TYPE_TAB_SWITCH) {
            $attempt->tab_switch_count++;
        } elseif ($violationType === ProctoringLog::TYPE_FULLSCREEN_EXIT) {
            $attempt->fullscreen_exit_count++;
        }

        $attempt->save();
    }

    /**
     * Get violation summary for an attempt
     */
    public function getViolationSummary(ExamAttempt $attempt): array
    {
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
    }

    /**
     * Mark multiple logs as reviewed
     */
    public function markAsReviewed(array $logIds, int $reviewerId, ?string $notes = null): int
    {
        return ProctoringLog::whereIn('id', $logIds)
            ->update([
                'is_reviewed' => true,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now(),
                'review_notes' => $notes,
            ]);
    }

    /**
     * Get snapshots for an attempt
     */
    public function getSnapshots(ExamAttempt $attempt): array
    {
        return $attempt->proctoringLogs()
            ->whereNotNull('snapshot_path')
            ->orderBy('created_at')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'url' => Storage::url($log->snapshot_path),
                    'violation_type' => $log->violation_type,
                    'violation_label' => $log->violation_label,
                    'severity' => $log->severity,
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();
    }

    /**
     * Check if camera was consistently enabled
     */
    public function checkCameraConsistency(ExamAttempt $attempt): array
    {
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
    }
}
