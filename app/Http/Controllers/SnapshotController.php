<?php

namespace App\Http\Controllers;

use App\Models\ProctoringLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class SnapshotController extends Controller
{
    /**
     * Serve a proctoring snapshot through authenticated route.
     * Only admins and the exam's teacher can view snapshots.
     */
    public function show(ProctoringLog $log): Response
    {
        $attempt = $log->attempt;

        if (!$attempt) {
            abort(404, 'Snapshot tidak ditemukan.');
        }

        if ((int) $log->user_id !== (int) $attempt->user_id) {
            abort(404, 'Snapshot tidak ditemukan.');
        }

        if (!auth()->user()->can('reviewProctoring', $attempt)) {
            abort(403, 'Anda tidak memiliki akses untuk melihat snapshot ini.');
        }

        // Check if file exists
        if (!$log->snapshot_path || !Storage::disk('local')->exists($log->snapshot_path)) {
            abort(404, 'Snapshot tidak ditemukan.');
        }

        try {
            $file = Storage::disk('local')->get($log->snapshot_path);
            $mimeType = Storage::disk('local')->mimeType($log->snapshot_path);

            if (!$file) {
                Log::warning('Snapshot file empty or unreadable: ' . $log->snapshot_path);
                abort(500, 'Snapshot tidak dapat dibaca.');
            }

            return response($file, 200, [
                'Content-Type' => $mimeType ?? 'image/jpeg',
                'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve snapshot: ' . $e->getMessage(), [
                'log_id' => $log->id,
                'path' => $log->snapshot_path,
            ]);
            
            abort(500, 'Gagal memuat snapshot. Silakan coba lagi.');
        }
    }
}
