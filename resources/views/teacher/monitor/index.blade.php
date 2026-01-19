@extends('layouts.teacher')

@section('title', 'Monitor Ujian')
@section('page-title', 'Monitor')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="page-header-title">
                                <h5 class="m-b-10">Monitor: {{ $exam->title }}</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Ujian</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Monitor</li>
                            </ul>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-success animate-pulse d-flex align-items-center">
                                <span class="me-2" style="width: 8px; height: 8px; background: #fff; border-radius: 50%; animation: pulse 1.5s infinite;"></span>
                                Live
                            </span>
                            <a href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-outline-secondary">
                                <i class="ph ph-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Total Peserta</p>
                            <h3 class="mb-0" id="total-enrolled">{{ $exam->course->students_count }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-primary">
                            <i class="ph-duotone ph-users text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Sedang Mengerjakan</p>
                            <h3 class="mb-0 text-success" id="in-progress">{{ $activeAttempts->count() }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-success">
                            <i class="ph-duotone ph-pencil-simple text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Sudah Selesai</p>
                            <h3 class="mb-0 text-info" id="submitted">{{ $submittedCount }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-info">
                            <i class="ph-duotone ph-check-circle text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Total Pelanggaran</p>
                            <h3 class="mb-0 text-danger" id="violations">{{ $totalViolations }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-danger">
                            <i class="ph-duotone ph-warning text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Token Display -->
    <div class="card mb-4 border-warning" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-warning-emphasis fw-medium mb-1">
                        <i class="ph-duotone ph-key me-2"></i>Token Akses Ujian
                    </p>
                    <h3 class="font-monospace fw-bold mb-0" style="letter-spacing: 4px; color: #92400e;">
                        {{ $exam->access_token }}
                    </h3>
                </div>
                <button onclick="copyToken('{{ $exam->access_token }}')" class="btn btn-warning">
                    <i class="ph ph-copy me-2"></i>Salin Token
                </button>
            </div>
        </div>
    </div>

    <!-- Active Attempts Grid -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="ph-duotone ph-user-focus text-success me-2"></i>Peserta Aktif
            </h5>
            <button onclick="refreshData()" class="btn btn-sm btn-outline-primary">
                <i class="ph ph-arrows-clockwise me-1"></i>Refresh
            </button>
        </div>
        <div class="card-body">
            @if($activeAttempts->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="ph-duotone ph-users-three text-muted" style="font-size: 64px; opacity: 0.3;"></i>
                    </div>
                    <h6 class="text-muted">Belum ada peserta yang sedang mengerjakan ujian</h6>
                    <p class="text-muted small">Peserta akan muncul di sini setelah memulai ujian</p>
                </div>
            @else
                <div class="row g-3" id="attempts-grid">
                    @foreach($activeAttempts as $attempt)
                        <div class="col-6 col-md-4 col-lg-3 col-xl-2" id="attempt-{{ $attempt->id }}">
                            <div class="card border {{ $attempt->violation_count > 0 ? 'border-danger' : '' }} h-100">
                                <!-- Camera Snapshot -->
                                <div class="position-relative" style="aspect-ratio: 4/3; background: #1f2937;">
                                    @if($attempt->latestSnapshot)
                                        <img src="{{ asset('storage/' . $attempt->latestSnapshot->snapshot_path) }}" 
                                             alt="Camera" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                            <i class="ph-duotone ph-video-camera" style="font-size: 40px; opacity: 0.3;"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Violation Badge -->
                                    @if($attempt->violation_count > 0)
                                        <span class="position-absolute top-0 end-0 m-2 badge bg-danger">
                                            {{ $attempt->violation_count }} <i class="ph ph-warning"></i>
                                        </span>
                                    @endif
                                    
                                    <!-- Camera Status -->
                                    <span class="position-absolute bottom-0 end-0 m-2">
                                        <span class="d-inline-block rounded-circle {{ $attempt->camera_enabled ? 'bg-success' : 'bg-danger' }}" 
                                              style="width: 10px; height: 10px;"></span>
                                    </span>
                                </div>
                                
                                <!-- Student Info -->
                                <div class="card-body p-2">
                                    <h6 class="mb-0 text-truncate small">{{ $attempt->user->name }}</h6>
                                    <small class="text-muted text-truncate d-block">{{ $attempt->user->email }}</small>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2 small text-muted">
                                        <span>{{ $attempt->answers_count ?? 0 }}/{{ $exam->questions_count }}</span>
                                        <span>{{ $attempt->formatted_remaining_time }}</span>
                                    </div>
                                    
                                    <div class="d-flex gap-1 mt-2">
                                        <a href="{{ route('teacher.monitor.attempt', $attempt) }}"
                                           class="btn btn-sm btn-light flex-grow-1">Detail</a>
                                        @if($attempt->violation_count > 0)
                                            <a href="{{ route('teacher.monitor.logs', $attempt) }}"
                                               class="btn btn-sm btn-danger">Log</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Violations -->
    @if($recentViolations->isNotEmpty())
        <div class="card border-danger">
            <div class="card-header bg-danger bg-opacity-10">
                <h5 class="card-title mb-0 text-danger">
                    <i class="ph-duotone ph-warning me-2"></i>Pelanggaran Terbaru
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            @foreach($recentViolations as $violation)
                                <tr>
                                    <td style="width: 60px;">
                                        @if($violation->snapshot_path)
                                            <img src="{{ asset('storage/' . $violation->snapshot_path) }}" 
                                                 alt="Snapshot" class="rounded" width="48" height="36" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 36px;">
                                                <i class="ph ph-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $violation->attempt->user->name }}</div>
                                        <div class="small">
                                            <span class="badge bg-danger me-1">{{ $violation->violation_type }}</span>
                                            <span class="text-muted">{{ $violation->description }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end text-muted small">
                                        {{ $violation->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('styles')
<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endpush

@push('scripts')
<script>
    function copyToken(token) {
        navigator.clipboard.writeText(token).then(() => {
            alert('Token disalin: ' + token);
        });
    }
    
    function refreshData() {
        window.location.reload();
    }
    
    // Auto refresh every 30 seconds
    setInterval(refreshData, 30000);
</script>
@endpush
