@extends('layouts.student')

@section('title', 'Detail Ujian')
@section('page-title', 'Detail Ujian')

@section('content')
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('student.exams.index') }}" class="btn btn-light btn-sm">
            <i class="ph ph-arrow-left me-1"></i>Kembali ke Daftar Ujian
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Exam Info Card -->
            <div class="card">
                <!-- Header -->
                <div class="card-header bg-primary text-white py-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="ph ph-file-text f-28"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 text-white f-w-600">{{ $exam->title }}</h4>
                            <small class="text-white-50">{{ $exam->course->name }}</small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Status Badge -->
                    <div class="mb-4">
                        @if($attempt && in_array($attempt->status, ['submitted', 'graded']))
                            <span class="badge bg-secondary px-3 py-2">
                                <i class="ph ph-check me-1"></i>Sudah Dikerjakan
                            </span>
                        @elseif($exam->isActive())
                            <span class="badge badge-soft-success px-3 py-2">
                                <span class="pulse-dot me-1"></span>Sedang Berlangsung
                            </span>
                        @elseif(!$exam->hasStarted())
                            <span class="badge badge-soft-warning px-3 py-2">
                                <i class="ph ph-clock me-1"></i>Belum Dimulai
                            </span>
                        @else
                            <span class="badge badge-soft-danger px-3 py-2">
                                <i class="ph ph-x me-1"></i>Sudah Berakhir
                            </span>
                        @endif
                    </div>
                    
                    <!-- Exam Details -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block mb-1">Waktu Mulai</small>
                                <span class="f-w-600">{{ $exam->start_time->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block mb-1">Waktu Selesai</small>
                                <span class="f-w-600">{{ $exam->end_time->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block mb-1">Durasi</small>
                                <span class="f-w-600">{{ $exam->duration_minutes }} menit</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block mb-1">Jumlah Soal</small>
                                <span class="f-w-600">{{ $exam->question_count }} soal</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block mb-1">Nilai Minimum Lulus</small>
                                <span class="f-w-600">{{ $exam->passing_score }}%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block mb-1">Total Poin</small>
                                <span class="f-w-600">{{ $exam->total_points }} poin</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Instructions -->
                    @if($exam->instructions)
                        <div class="mb-4">
                            <h6 class="f-w-600 mb-2">Petunjuk Ujian</h6>
                            <div class="bg-light rounded-3 p-3 f-14">
                                {!! nl2br(e($exam->instructions)) !!}
                            </div>
                        </div>
                    @endif
                    
                    <!-- Proctoring Requirements -->
                    <div class="alert alert-warning mb-4">
                        <h6 class="f-w-600 mb-2"><i class="ph ph-warning me-1"></i>Persyaratan Proctoring</h6>
                        <ul class="mb-0 ps-3 f-14">
                            @if($exam->require_camera)
                                <li>Akses kamera diperlukan untuk pengawasan</li>
                            @endif
                            @if($exam->require_fullscreen)
                                <li>Mode fullscreen akan diaktifkan</li>
                            @endif
                            <li>Dilarang membuka tab/aplikasi lain</li>
                            <li>Dilarang copy/paste</li>
                            <li>Maksimal {{ $exam->max_violations }} pelanggaran sebelum auto-submit</li>
                        </ul>
                    </div>
                    
                    <!-- Start Exam Form -->
                    @if($exam->isActive() && (!$attempt || $attempt->status === 'not_started'))
                        <form action="{{ route('student.exams.start', $exam) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="access_token" class="form-label f-w-500">Token Akses</label>
                                <input type="text" 
                                       name="access_token" 
                                       id="access_token"
                                       placeholder="Masukkan token yang diberikan pengawas"
                                       class="form-control @error('access_token') is-invalid @enderror"
                                       required>
                                @error('access_token')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agree" name="agree" required>
                                    <label class="form-check-label f-14" for="agree">
                                        Saya memahami dan menyetujui aturan ujian di atas
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ph ph-play me-1"></i>Mulai Ujian
                            </button>
                        </form>
                    @elseif($attempt && $attempt->isInProgress())
                        <a href="{{ route('student.exams.take', $attempt) }}" class="btn btn-success w-100">
                            <i class="ph ph-play me-1"></i>Lanjutkan Ujian
                        </a>
                    @elseif($attempt && $attempt->isSubmitted())
                        <a href="{{ route('student.exams.result', $attempt) }}" class="btn btn-secondary w-100">
                            <i class="ph ph-eye me-1"></i>Lihat Hasil
                        </a>
                    @else
                        <div class="text-center py-3 text-muted">
                            Ujian tidak tersedia saat ini
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .pulse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: currentColor;
        border-radius: 50%;
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
        100% { opacity: 1; transform: scale(1); }
    }
</style>
@endpush
