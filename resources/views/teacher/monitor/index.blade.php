@extends('layouts.teacher')

@section('title', 'Monitor Ujian')
@section('page-title', 'Monitor')

@section('content')
    <!-- Page Header -->
    <div class="card zaf-hero mb-4 zaf-reveal">
        <div class="card-body p-4" style="position: relative; z-index: 1;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <p class="hero-kicker mb-2">Invigilation Center</p>
                    <h5 class="hero-title mb-1">Monitor: {{ $exam->title }}</h5>
                    <p class="mb-0" style="color: rgba(248,250,252,0.82);">Pantau aktivitas peserta dan pelanggaran secara real-time.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="hero-chip">
                        <span class="me-2" style="width: 8px; height: 8px; background: #fff; border-radius: 50%; animation: pulse 1.5s infinite;"></span>
                        Live
                    </span>
                    <a href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-light btn-sm">
                        <i class="ph ph-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="row mb-4 zaf-reveal">
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Total Peserta</p>
                            <h3 class="mb-0" id="total-enrolled">{{ $exam->course->students_count }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-primary">
                            <i class="ph ph-users text-primary"></i>
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
                            <i class="ph ph-pencil-simple text-success"></i>
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
                            <i class="ph ph-check-circle text-info"></i>
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
                            <i class="ph ph-warning text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Token Display -->
    <div class="card mb-4 border-warning zaf-reveal" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-warning-emphasis fw-medium mb-1">
                        <i class="ph ph-key me-2"></i>Token Akses Ujian
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
    <div class="card mb-4 zaf-reveal">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="ph ph-user-large text-success me-2"></i>Peserta Aktif
            </h5>
            <button onclick="refreshData()" class="btn btn-sm btn-outline-primary">
                <i class="ph ph-arrows-clockwise me-1"></i>Refresh
            </button>
        </div>
        <div class="card-body">
            @if($activeAttempts->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="ph ph-users text-muted" style="font-size: 64px; opacity: 0.3;"></i>
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
                                        <img src="{{ route('proctoring.snapshot.view', $attempt->latestSnapshot->id) }}" 
                                             alt="Camera" class="w-100 h-100 object-fit-cover"
                                             onerror="this.parentElement.innerHTML='<div class=\'w-100 h-100 d-flex align-items-center justify-content-center text-muted\'><i class=\'ph ph-video-camera\' style=\'font-size: 40px; opacity: 0.3;\'></i></div>'">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                            <i class="ph ph-video-camera" style="font-size: 40px; opacity: 0.3;"></i>
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
                                        <a href="{{ route('teacher.monitor.attempt', [$exam, $attempt]) }}"
                                           class="btn btn-sm btn-light flex-grow-1">Detail</a>
                                        @if($attempt->violation_count > 0)
                                            <a href="{{ route('teacher.monitor.logs', [$exam, $attempt]) }}"
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
                    <i class="ph ph-warning me-2"></i>Pelanggaran Terbaru
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody id="violations-tbody">
                            @foreach($recentViolations as $violation)
                                <tr>
                                    <td style="width: 60px;">
                                        @if($violation->snapshot_path)
                                            <img src="{{ route('proctoring.snapshot.view', $violation->id) }}" 
                                                 alt="Snapshot" class="rounded" width="48" height="36" style="object-fit: cover;"
                                                 onerror="this.parentElement.innerHTML='<div class=\'bg-light rounded d-flex align-items-center justify-content-center\' style=\'width: 48px; height: 36px;\'><i class=\'ph ph-image text-muted\'></i></div>'">
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

    let previousViolationCount = Number('{{ $totalViolations ?? 0 }}');

    async function refreshData() {
        try {
            const res = await fetch("{{ route('teacher.monitor.live', $exam) }}", {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const data = await res.json();

            // ── Update stat cards ──
            document.getElementById('total-enrolled').textContent = data.stats.total;
            document.getElementById('in-progress').textContent = data.stats.in_progress;
            document.getElementById('submitted').textContent = data.stats.submitted;
            document.getElementById('violations').textContent = data.stats.violations;

            // ── Real-time violation notification ──
            if (data.stats.violations > previousViolationCount) {
                const diff = data.stats.violations - previousViolationCount;
                showViolationToast(diff, data.violations[0]);
            }
            previousViolationCount = data.stats.violations;

            // ── Update active attempts grid ──
            const grid = document.getElementById('attempts-grid');
            if (grid && data.active_attempts) {
                if (data.active_attempts.length === 0) {
                    grid.innerHTML = '<div class="col-12"><div class="text-center text-muted py-5"><i class="ph ph-monitor fs-1 d-block mb-3"></i>Belum ada peserta aktif</div></div>';
                } else {
                    grid.innerHTML = data.active_attempts.map(a => `
                        <div class="col-6 col-md-4 col-lg-3 col-xl-2" id="attempt-${a.id}">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-3 text-center">
                                    <div class="position-relative d-inline-block mb-2">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:48px;height:48px;margin:0 auto;">
                                            <i class="ph ph-user fs-4 text-muted"></i>
                                        </div>
                                        ${a.violation_count > 0 ? `<span class="position-absolute top-0 end-0 badge bg-danger rounded-pill" style="font-size:0.65rem">${a.violation_count}</span>` : ''}
                                        <span class="position-absolute bottom-0 end-0 rounded-circle border border-2 border-white" style="width:12px;height:12px;background:${a.camera_enabled ? '#22c55e' : '#ef4444'}"></span>
                                    </div>
                                    <div class="fw-semibold text-truncate" style="font-size:0.82rem">${a.name}</div>
                                    <div class="text-muted text-truncate" style="font-size:0.7rem">${a.email}</div>
                                    <div class="mt-2 d-flex justify-content-center gap-2" style="font-size:0.75rem">
                                        <span class="badge bg-light text-dark">${a.answers_count}/${a.question_count}</span>
                                        ${a.remaining_time ? `<span class="badge bg-light text-dark">${Math.floor(a.remaining_time/60)}m</span>` : ''}
                                    </div>
                                    <div class="mt-2">
                                        <a href="${a.monitor_url}" class="btn btn-sm btn-outline-primary" style="font-size:0.7rem"><i class="ph ph-eye me-1"></i>Detail</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
            }

            // ── Update recent violations ──
            const violTable = document.getElementById('violations-tbody');
            if (violTable && data.violations) {
                violTable.innerHTML = data.violations.map(v => `
                    <tr>
                        <td style="width:50px">
                            ${v.snapshot_url ? `<img src="${v.snapshot_url}" class="rounded" style="width:40px;height:40px;object-fit:cover" alt="" onerror="this.parentElement.innerHTML='<div class=\'bg-light rounded d-flex align-items-center justify-content-center\' style=\'width:40px;height:40px\'><i class=\'ph ph-image text-muted\'></i></div>'">` : '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px"><i class="ph ph-video-camera-slash text-muted"></i></div>'}
                        </td>
                        <td>
                            <div class="fw-medium">${v.user_name}</div>
                            <div class="small"><span class="badge bg-danger me-1">${v.type}</span><span class="text-muted">${v.description || ''}</span></div>
                        </td>
                        <td class="text-end text-muted small">${v.time_ago}</td>
                    </tr>
                `).join('');
            }
        } catch(e) {
            console.warn('Monitor refresh failed:', e);
        }
    }

    // ── Toast Notification for new violations ──
    function showViolationToast(count, latestViolation) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '1090';
        toast.innerHTML = `
            <div class="toast show border-0 shadow-lg" role="alert">
                <div class="toast-header bg-danger text-white">
                    <i class="ph ph-warning me-2"></i>
                    <strong class="me-auto">Pelanggaran Baru</strong>
                    <button type="button" class="btn-close btn-close-white" onclick="this.closest('.position-fixed').remove()"></button>
                </div>
                <div class="toast-body">
                    <strong>${count}</strong> pelanggaran baru terdeteksi.
                    ${latestViolation ? `<br><small class="text-muted">${latestViolation.user_name}: ${latestViolation.type}</small>` : ''}
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        // Play notification sound
        try { new Audio('data:audio/wav;base64,UklGRl9vT19teleported...').play().catch(()=>{}); } catch(e){}
        setTimeout(() => toast.remove(), 8000);
    }

    // Auto refresh every 15 seconds (AJAX, no full reload)
    setInterval(refreshData, 15000);
</script>
@endpush
