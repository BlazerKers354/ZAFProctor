@extends('layouts.student')

@section('title', 'Daftar Ujian')
@section('page-title', 'Daftar Ujian')

@section('content')
    <!-- Page Header -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="mb-1 f-w-600">Ujian Tersedia</h4>
            <p class="text-muted mb-0 f-14">Pilih ujian yang ingin Anda kerjakan</p>
        </div>
        <span class="badge badge-soft-primary px-3 py-2 f-14">
            <i class="ph ph-clipboard-text me-1"></i>{{ $availableExams->count() }} Ujian
        </span>
    </div>

    @if($availableExams->isEmpty())
        <!-- Empty State -->
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 96px; height: 96px;">
                    <i class="ph ph-clipboard-text f-48 text-muted"></i>
                </div>
                <h5 class="mb-2 f-w-600">Tidak Ada Ujian Tersedia</h5>
                <p class="text-muted mb-4 mx-auto" style="max-width: 400px;">Belum ada ujian yang tersedia untuk Anda saat ini. Cek kembali nanti atau hubungi guru Anda.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="ph ph-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>
    @else
        <!-- Exam Cards Grid -->
        <div class="row g-4">
            @foreach($availableExams as $exam)
                <div class="col-12 col-lg-6">
                    <div class="card exam-card h-100">
                        <!-- Card Header with Status -->
                        <div class="card-header py-3">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <!-- Exam Icon -->
                                    <div class="exam-icon bg-primary bg-opacity-10 text-primary">
                                        <i class="ph ph-file-text"></i>
                                    </div>
                                    
                                    <!-- Exam Title & Course -->
                                    <div>
                                        <h6 class="mb-1 f-w-600">{{ Str::limit($exam->title, 35) }}</h6>
                                        <small class="text-muted">{{ $exam->course?->name ?? 'Ujian Umum' }}</small>
                                    </div>
                                </div>
                                
                                <!-- Status Badge -->
                                @if($exam->user_attempt && $exam->user_attempt->isInProgress())
                                    <span class="badge bg-warning">
                                        <span class="pulse-dot me-1"></span>Dikerjakan
                                    </span>
                                @elseif($exam->attempt_count > 0 && !$exam->can_retry)
                                    <span class="badge bg-secondary">
                                        <i class="ph ph-check me-1"></i>Selesai
                                    </span>
                                @elseif($exam->attempt_count > 0 && $exam->can_retry)
                                    <span class="badge badge-soft-primary">
                                        <i class="ph ph-arrow-clockwise me-1"></i>Bisa Retry
                                    </span>
                                @elseif($exam->isActive())
                                    <span class="badge badge-soft-success">
                                        <span class="pulse-dot me-1"></span>Tersedia
                                    </span>
                                @elseif(!$exam->hasStarted())
                                    <span class="badge badge-soft-info">
                                        <i class="ph ph-clock me-1"></i>Mendatang
                                    </span>
                                @else
                                    <span class="badge badge-soft-danger">
                                        <i class="ph ph-x me-1"></i>Berakhir
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- Exam Meta Info -->
                            <div class="row g-2 mb-4">
                                <div class="col-4">
                                    <div class="text-center p-2 bg-light rounded-3">
                                        <i class="ph ph-calendar f-20 text-muted mb-1 d-block"></i>
                                        <small class="text-muted d-block">Mulai</small>
                                        <span class="f-14 f-w-600">{{ $exam->start_time?->format('d M') ?? 'Fleksibel' }}</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center p-2 bg-light rounded-3">
                                        <i class="ph ph-clock f-20 text-muted mb-1 d-block"></i>
                                        <small class="text-muted d-block">Durasi</small>
                                        <span class="f-14 f-w-600">{{ $exam->duration }} min</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center p-2 bg-light rounded-3">
                                        <i class="ph ph-question f-20 text-muted mb-1 d-block"></i>
                                        <small class="text-muted d-block">Soal</small>
                                        <span class="f-14 f-w-600">{{ $exam->question_count }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Schedule Info -->
                            <div class="d-flex align-items-center gap-2 p-3 bg-primary bg-opacity-10 rounded-3 mb-4">
                                <i class="ph ph-calendar-blank text-primary f-20"></i>
                                <span class="text-primary f-14">
                                    @if($exam->type === 'scheduled' && $exam->start_time && $exam->end_time)
                                        {{ $exam->start_time->format('d M Y, H:i') }} - {{ $exam->end_time->format('d M Y, H:i') }}
                                    @else
                                        <i class="ph ph-infinity me-1"></i>Ujian Fleksibel - Dapat dikerjakan kapan saja
                                    @endif
                                </span>
                            </div>

                            <!-- Action Button -->
                            @if($exam->user_attempt && $exam->user_attempt->isInProgress())
                                <a href="{{ route('student.exams.take', $exam->user_attempt) }}" class="btn btn-warning w-100">
                                    <i class="ph ph-play me-1"></i>Lanjutkan Ujian
                                </a>
                            @elseif($exam->attempt_count > 0 && $exam->best_attempt)
                                <div class="d-flex align-items-center justify-content-between">
                                    @php
                                        $scoreBg = $exam->best_attempt->is_passed ? 'bg-success' : 'bg-danger';
                                    @endphp
                                    <div class="score-badge {{ $scoreBg }} text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                        <span class="f-20 f-w-600">{{ number_format($exam->best_attempt->percentage, 0) }}%</span>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <small class="text-muted">
                                            {{ $exam->attempt_count }}/{{ $exam->max_attempts == 0 ? '∞' : $exam->max_attempts }} percobaan
                                        </small>
                                        @if($exam->can_retry && $exam->isActive())
                                            <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-sm btn-primary">
                                                <i class="ph ph-arrow-clockwise me-1"></i>Coba Lagi
                                            </a>
                                        @else
                                            <a href="{{ route('student.exams.result', $exam->best_attempt) }}" class="btn btn-sm btn-light-primary">
                                                <i class="ph ph-eye me-1"></i>Lihat Hasil
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @elseif($exam->isActive())
                                <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-primary w-100">
                                    <i class="ph ph-play me-1"></i>Mulai Ujian
                                </a>
                            @else
                                <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-secondary w-100">
                                    <i class="ph ph-eye me-1"></i>Lihat Detail
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
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
        0% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.5;
            transform: scale(1.2);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>
@endpush
