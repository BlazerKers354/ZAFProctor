@extends('layouts.teacher')

@section('title', 'Hasil Ujian - ' . $exam->title)
@section('page-title', 'Hasil Ujian')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="page-header-title">
                                <h5 class="m-b-10">Hasil Ujian</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Ujian</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.show', $exam) }}">{{ Str::limit($exam->title, 20) }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Hasil</li>
                            </ul>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('teacher.exams.export', $exam) }}" class="btn btn-success">
                                <i class="ph ph-download me-2"></i>Export CSV
                            </a>
                            <a href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-outline-secondary">
                                <i class="ph ph-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1">{{ $exam->title }}</h4>
                    <p class="text-muted mb-0">
                        <i class="ph ph-book me-1"></i>{{ $exam->course->name }}
                        <span class="mx-2">|</span>
                        <i class="ph ph-clock me-1"></i>{{ $exam->duration }} menit
                        <span class="mx-2">|</span>
                        <i class="ph ph-clipboard-text me-1"></i>{{ $exam->questions->count() }} soal
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    @if($exam->settings?->passing_score)
                        <span class="badge bg-light-primary">
                            <i class="ph ph-target me-1"></i>KKM: {{ $exam->settings->passing_score }}%
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-primary">
                                <i class="ph-duotone ph-users fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Peserta</h6>
                            <h4 class="mb-0 text-primary">{{ $statistics['total_participants'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-success">
                                <i class="ph-duotone ph-check-circle fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Selesai</h6>
                            <h4 class="mb-0 text-success">{{ $statistics['completed_attempts'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-info">
                                <i class="ph-duotone ph-chart-line-up fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Rata-rata</h6>
                            <h4 class="mb-0 text-info">{{ number_format($statistics['average_score'] ?? 0, 1) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-warning">
                                <i class="ph-duotone ph-trophy fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Nilai Tertinggi</h6>
                            <h4 class="mb-0 text-warning">{{ number_format($statistics['highest_score'] ?? 0, 1) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ph-duotone ph-list-numbers text-primary me-2"></i>Daftar Hasil</h5>
                <div>
                    <span class="badge bg-light-success me-2">
                        <i class="ph ph-check me-1"></i>Lulus: {{ $attempts->where('percentage', '>=', $exam->settings?->passing_score ?? 60)->count() }}
                    </span>
                    <span class="badge bg-light-danger">
                        <i class="ph ph-x me-1"></i>Tidak Lulus: {{ $attempts->where('percentage', '<', $exam->settings?->passing_score ?? 60)->count() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($attempts->isEmpty())
                <div class="text-center py-5">
                    <i class="ph-duotone ph-clipboard-text text-muted mb-3" style="font-size: 64px; opacity: 0.3;"></i>
                    <h5 class="text-muted">Belum Ada Peserta</h5>
                    <p class="text-muted mb-0">Belum ada siswa yang mengikuti ujian ini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Waktu Mulai</th>
                                <th>Waktu Selesai</th>
                                <th>Durasi</th>
                                <th>Pelanggaran</th>
                                <th>Nilai</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $index => $attempt)
                                @php
                                    $passingScore = $exam->settings?->passing_score ?? 60;
                                    $isPassed = $attempt->percentage >= $passingScore;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2 bg-light-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                <span class="fw-medium text-primary">{{ strtoupper(substr($attempt->student->name ?? 'U', 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-medium">{{ $attempt->student->name ?? 'Unknown' }}</p>
                                                <small class="text-muted">{{ $attempt->student->nis ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $attempt->student->schoolClass?->name ?? '-' }}</td>
                                    <td>
                                        @if($attempt->started_at)
                                            {{ $attempt->started_at->format('d/m/Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->finished_at)
                                            {{ $attempt->finished_at->format('d/m/Y H:i') }}
                                        @else
                                            <span class="badge bg-light-warning">Belum selesai</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->started_at && $attempt->finished_at)
                                            {{ round($attempt->started_at->diffInMinutes($attempt->finished_at), 0) }} menit
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if(($attempt->violation_count ?? 0) > 0)
                                            <span class="badge bg-light-danger">
                                                <i class="ph ph-warning me-1"></i>{{ $attempt->violation_count }}
                                            </span>
                                        @else
                                            <span class="badge bg-light-success">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->score !== null)
                                            <span class="badge {{ $isPassed ? 'bg-success' : 'bg-danger' }} fs-6">
                                                {{ number_format($attempt->score, 1) }}
                                            </span>
                                        @else
                                            <span class="badge bg-light-secondary">Belum dinilai</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($attempt->status)
                                            @case('completed')
                                                <span class="badge bg-light-success">Selesai</span>
                                                @break
                                            @case('in_progress')
                                                <span class="badge bg-light-info">Berlangsung</span>
                                                @break
                                            @case('terminated')
                                                <span class="badge bg-light-danger">Dihentikan</span>
                                                @break
                                            @case('timeout')
                                                <span class="badge bg-light-warning">Timeout</span>
                                                @break
                                            @default
                                                <span class="badge bg-light-secondary">{{ ucfirst($attempt->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                                <i class="ph ph-dots-three-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('teacher.monitor.attempt', ['exam' => $exam, 'attempt' => $attempt]) }}">
                                                        <i class="ph ph-eye me-2"></i>Lihat Detail
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('teacher.exams.grade', $attempt) }}">
                                                        <i class="ph ph-pencil-simple me-2"></i>Nilai Manual
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('teacher.monitor.logs', ['exam' => $exam, 'attempt' => $attempt]) }}">
                                                        <i class="ph ph-list-bullets me-2"></i>Log Aktivitas
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Score Distribution Chart -->
    @if($attempts->isNotEmpty())
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ph-duotone ph-chart-bar text-info me-2"></i>Distribusi Nilai</h5>
                </div>
                <div class="card-body">
                    @php
                        $scoreRanges = [
                            '0-20' => $attempts->whereBetween('score', [0, 20])->count(),
                            '21-40' => $attempts->whereBetween('score', [21, 40])->count(),
                            '41-60' => $attempts->whereBetween('score', [41, 60])->count(),
                            '61-80' => $attempts->whereBetween('score', [61, 80])->count(),
                            '81-100' => $attempts->whereBetween('score', [81, 100])->count(),
                        ];
                        $maxCount = max($scoreRanges) ?: 1;
                    @endphp
                    
                    @foreach($scoreRanges as $range => $count)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $range }}</span>
                                <span class="text-muted">{{ $count }} siswa</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-primary" style="width: {{ ($count / $maxCount) * 100 }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ph-duotone ph-chart-pie text-success me-2"></i>Statistik Kelulusan</h5>
                </div>
                <div class="card-body">
                    @php
                        $passedCount = $attempts->where('percentage', '>=', $exam->settings?->passing_score ?? 60)->count();
                        $failedCount = $attempts->count() - $passedCount;
                        $passPercentage = $attempts->count() > 0 ? round(($passedCount / $attempts->count()) * 100, 1) : 0;
                    @endphp
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <svg viewBox="0 0 36 36" width="150" height="150">
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                      fill="none" stroke="#e9ecef" stroke-width="3" />
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                      fill="none" stroke="#10b981" stroke-width="3"
                                      stroke-dasharray="{{ $passPercentage }}, 100" />
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <h3 class="mb-0">{{ $passPercentage }}%</h3>
                                <small class="text-muted">Lulus</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="p-3 bg-light-success rounded">
                                <h4 class="mb-0 text-success">{{ $passedCount }}</h4>
                                <small class="text-muted">Lulus</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light-danger rounded">
                                <h4 class="mb-0 text-danger">{{ $failedCount }}</h4>
                                <small class="text-muted">Tidak Lulus</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
