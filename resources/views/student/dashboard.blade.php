@extends('layouts.student')

@section('title', 'Dashboard Siswa')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Welcome Banner -->
    <div class="card bg-primary text-white mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="ph ph-student f-36"></i>
                        </div>
                        <div>
                            <p class="text-white-50 mb-1 f-14">Selamat datang kembali,</p>
                            <h3 class="mb-1 text-white f-w-600">{{ auth()->user()->name }}</h3>
                            <p class="text-white-50 mb-0 f-14">Semoga harimu menyenangkan! 🎉</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="d-inline-flex gap-2">
                        <span class="badge bg-white bg-opacity-25 px-3 py-2">
                            <i class="ph ph-graduation-cap me-1"></i>{{ $stats['class_name'] }}
                        </span>
                        <span class="badge bg-white bg-opacity-25 px-3 py-2">
                            <i class="ph ph-calendar me-1"></i>{{ now()->locale('id')->translatedFormat('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Ujian Mendatang -->
        <div class="col-6 col-lg-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                            <i class="ph ph-clock"></i>
                        </div>
                        <span class="badge badge-soft-warning">
                            <i class="ph ph-calendar-blank me-1"></i>Jadwal
                        </span>
                    </div>
                    <h3 class="stats-value text-dark">{{ $stats['upcoming_exams'] }}</h3>
                    <p class="stats-label mb-0">Ujian Mendatang</p>
                </div>
            </div>
        </div>

        <!-- Ujian Aktif -->
        <div class="col-6 col-lg-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="stats-icon bg-success bg-opacity-10 text-success">
                            <i class="ph ph-lightning"></i>
                        </div>
                        @if($stats['active_exams'] > 0)
                            <span class="badge bg-success">
                                <span class="pulse-dot me-1"></span>LIVE
                            </span>
                        @endif
                    </div>
                    <h3 class="stats-value text-dark">{{ $stats['active_exams'] }}</h3>
                    <p class="stats-label mb-0">Ujian Aktif</p>
                </div>
            </div>
        </div>

        <!-- Ujian Selesai -->
        <div class="col-6 col-lg-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="stats-icon bg-info bg-opacity-10 text-info">
                            <i class="ph ph-check-circle"></i>
                        </div>
                        <span class="badge badge-soft-info">
                            <i class="ph ph-check me-1"></i>Selesai
                        </span>
                    </div>
                    <h3 class="stats-value text-dark">{{ $stats['completed_exams'] }}</h3>
                    <p class="stats-label mb-0">Ujian Selesai</p>
                </div>
            </div>
        </div>

        <!-- Rata-rata Nilai -->
        <div class="col-6 col-lg-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="stats-icon bg-brand-color-2 bg-opacity-10" style="color: var(--pc-brand-color-2);">
                            <i class="ph ph-star"></i>
                        </div>
                        @php
                            $scoreLabel = $stats['average_score'] >= 80 ? 'Excellent' : ($stats['average_score'] >= 70 ? 'Good' : 'Keep Going');
                            $scoreBadge = $stats['average_score'] >= 80 ? 'badge-soft-success' : ($stats['average_score'] >= 70 ? 'badge-soft-warning' : 'badge-soft-danger');
                        @endphp
                        <span class="badge {{ $scoreBadge }}">{{ $scoreLabel }}</span>
                    </div>
                    <h3 class="stats-value text-dark">{{ number_format($stats['average_score'], 1) }}</h3>
                    <p class="stats-label mb-0">Rata-rata Nilai</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Active Exams Section -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-25 rounded-3 p-2">
                                <i class="ph ph-lightning f-20"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-white f-w-600">Ujian Tersedia</h5>
                                <small class="text-white-50">Ujian yang dapat Anda kerjakan sekarang</small>
                            </div>
                        </div>
                        <a href="{{ route('student.exams.index') }}" class="btn btn-light btn-sm">
                            Lihat Semua <i class="ph ph-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse($activeExams as $exam)
                        <div class="exam-item p-4 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="exam-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="ph ph-file-text f-24"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 f-w-600">{{ $exam->title }}</h6>
                                    <p class="text-muted mb-2 f-12">{{ $exam->course?->name ?? 'Ujian Umum' }}</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-light text-dark">
                                            <i class="ph ph-clock me-1"></i>{{ $exam->duration }} menit
                                        </span>
                                        <span class="badge bg-light text-dark">
                                            <i class="ph ph-question me-1"></i>{{ $exam->questions_count ?? $exam->questions()->count() }} Soal
                                        </span>
                                        @if($exam->start_time)
                                            <span class="badge badge-soft-success">
                                                <span class="pulse-dot me-1"></span>Aktif hingga {{ $exam->end_time?->format('H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('student.exams.show', $exam) }}" class="btn btn-primary">
                                    <i class="ph ph-play me-1"></i>Mulai
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="ph ph-clipboard-text f-36 text-muted"></i>
                            </div>
                            <h6 class="mb-1">Tidak Ada Ujian Aktif</h6>
                            <p class="text-muted mb-0 f-14">Belum ada ujian yang tersedia untuk dikerjakan saat ini</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Upcoming Exams Sidebar -->
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-white py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-3 p-2">
                            <i class="ph ph-calendar f-20"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 text-white f-w-600">Jadwal Ujian</h5>
                            <small class="text-white-50">Ujian mendatang</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse($upcomingExams as $exam)
                        <div class="schedule-item p-3 border-bottom">
                            <div class="d-flex gap-3">
                                <div class="schedule-date text-center bg-warning bg-opacity-10 rounded-3 p-2" style="min-width: 56px;">
                                    <span class="d-block f-20 f-w-600 text-warning">{{ $exam->start_time?->format('d') ?? '--' }}</span>
                                    <span class="d-block f-10 f-w-600 text-warning text-uppercase">{{ $exam->start_time?->format('M') ?? '---' }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 f-14 f-w-600">{{ Str::limit($exam->title, 25) }}</h6>
                                    <p class="text-muted mb-2 f-12">{{ $exam->course?->name ?? 'Ujian Umum' }}</p>
                                    <div class="d-flex gap-2">
                                        <span class="badge badge-soft-warning">
                                            <i class="ph ph-clock me-1"></i>{{ $exam->start_time?->format('H:i') ?? 'TBA' }}
                                        </span>
                                        <span class="badge bg-light text-muted">{{ $exam->duration }} min</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                <i class="ph ph-calendar-blank f-28 text-warning"></i>
                            </div>
                            <p class="text-muted mb-0 f-14">Tidak ada jadwal ujian mendatang</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Results Section -->
    <div class="card table-card mt-4">
        <div class="card-header bg-success text-white py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-25 rounded-3 p-2">
                    <i class="ph ph-chart-bar f-20"></i>
                </div>
                <div>
                    <h5 class="mb-0 text-white f-w-600">Hasil Ujian Terbaru</h5>
                    <small class="text-white-50">Riwayat nilai ujian Anda</small>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Ujian</th>
                        <th class="d-none d-sm-table-cell">Tanggal</th>
                        <th class="text-center">Nilai</th>
                        <th class="text-center d-none d-md-table-cell">Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentResults as $attempt)
                        @if($attempt->exam)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success bg-opacity-10 rounded-3 p-2">
                                        <i class="ph ph-file-text f-20 text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 f-14 f-w-600">{{ Str::limit($attempt->exam->title, 30) }}</h6>
                                        <small class="text-muted">{{ $attempt->exam->course?->name ?? 'Ujian Umum' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <span class="f-14">{{ $attempt->submitted_at?->format('d M Y') ?? '-' }}</span><br>
                                <small class="text-muted">{{ $attempt->submitted_at?->format('H:i') ?? '' }} WIB</small>
                            </td>
                            <td class="text-center">
                                @if($attempt->isSubmitted() && $attempt->score !== null)
                                    @php
                                        $scoreBg = $attempt->percentage >= 80 ? 'bg-success' : ($attempt->percentage >= 70 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <div class="score-badge {{ $scoreBg }} text-white rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <span class="f-w-600">{{ number_format($attempt->percentage, 0) }}</span>
                                    </div>
                                @else
                                    <div class="bg-light rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                @if($attempt->status === 'graded')
                                    <span class="badge badge-soft-success">
                                        <i class="ph ph-check me-1"></i>Dinilai
                                    </span>
                                @elseif($attempt->isSubmitted() && $attempt->score !== null)
                                    <span class="badge badge-soft-info">
                                        <i class="ph ph-check-circle me-1"></i>Selesai
                                    </span>
                                @else
                                    <span class="badge badge-soft-warning">
                                        <i class="ph ph-clock me-1"></i>Menunggu
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($attempt->isSubmitted() && $attempt->score !== null)
                                    <a href="{{ route('student.exams.result', $attempt) }}" class="btn btn-light-primary btn-sm">
                                        <i class="ph ph-eye me-1"></i>Detail
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="ph ph-file-text f-36 text-muted"></i>
                                </div>
                                <h6 class="mb-1">Belum Ada Hasil Ujian</h6>
                                <p class="text-muted mb-0 f-14">Selesaikan ujian untuk melihat hasilnya di sini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .exam-item:hover {
        background-color: #f8f9fa;
    }
    
    .schedule-item:hover {
        background-color: #fff8e6;
    }
    
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
