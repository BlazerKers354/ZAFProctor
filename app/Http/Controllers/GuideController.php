<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GuideController extends Controller
{
    public function download(Request $request)
    {
        try {
            $pdf = Pdf::loadView('guides.panduan-pengguna');
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->download('Panduan-Pengguna-ZAFProctor.pdf');
        } catch (\Exception $e) {
            Log::error('Failed to generate user guide PDF: ' . $e->getMessage());
            
            return back()->with('error', 'Gagal mengunduh panduan. Silakan coba lagi nanti.');
        }
    }
}
