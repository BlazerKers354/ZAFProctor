<?php

namespace App\Http\Controllers;

use App\Models\ProctoringLog;
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
        $user = auth()->user();

        // Authorization: admin, or the teacher who created the exam
        $examCreatorId = $log->attempt?->exam?->created_by;
        
        if (!$user->isAdmin() && $user->id !== $examCreatorId) {
            abort(403, 'Anda tidak memiliki akses untuk melihat snapshot ini.');
        }

        // Check if file exists
        if (!$log->snapshot_path || !Storage::disk('local')->exists($log->snapshot_path)) {
            abort(404, 'Snapshot tidak ditemukan.');
        }

        $file = Storage::disk('local')->get($log->snapshot_path);
        $mimeType = Storage::disk('local')->mimeType($log->snapshot_path);

        return response($file, 200, [
            'Content-Type' => $mimeType ?? 'image/jpeg',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
