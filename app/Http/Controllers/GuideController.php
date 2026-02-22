<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function download(Request $request)
    {
        $pdf = Pdf::loadView('guides.panduan-pengguna');
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('Panduan-Pengguna-ZAFProctor.pdf');
    }
}
