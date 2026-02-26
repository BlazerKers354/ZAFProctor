@extends('layouts.teacher')

@section('title', 'Log Pelanggaran')
@section('page-title', 'Log Pelanggaran')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="page-header-title">
                                <h5 class="m-b-10">Log Pelanggaran: {{ $attempt->user->name }}</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph ph-house"></i></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Ujian</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.monitor.index', $exam) }}">Monitor</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Log</li>
                            </ul>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('teacher.monitor.attempt', [$exam, $attempt]) }}" class="btn btn-outline-primary">
                                <i class="ph ph-user me-2"></i>Lihat Detail
                            </a>
                            <a href="{{ route('teacher.monitor.index', $exam) }}" class="btn btn-outline-secondary">
                                <i class="ph ph-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Total Pelanggaran</p>
                            <h3 class="mb-0 text-danger">{{ $attempt->violation_count }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-danger">
                            <i class="ph ph-warning text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Tab Switch</p>
                            <h3 class="mb-0 text-warning">{{ $attempt->tab_switch_count ?? 0 }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-warning">
                            <i class="ph ph-browsers text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Keluar Fullscreen</p>
                            <h3 class="mb-0 text-info">{{ $attempt->fullscreen_exit_count ?? 0 }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-info">
                            <i class="ph ph-arrows-out text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="ph ph-list-dashes me-2"></i>Riwayat Pelanggaran
            </h5>
            <span class="badge bg-secondary">{{ $logs->total() }} Log</span>
        </div>
        <div class="card-body p-0">
            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Waktu</th>
                                <th style="width: 150px;">Jenis</th>
                                <th>Deskripsi</th>
                                <th style="width: 100px;">Severity</th>
                                <th style="width: 120px;">Snapshot</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>
                                        <small class="text-muted">{{ $log->created_at->format('d M Y') }}</small><br>
                                        <strong>{{ $log->created_at->format('H:i:s') }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'tab_switch' => 'warning',
                                                'fullscreen_exit' => 'info',
                                                'face_not_detected' => 'danger',
                                                'multiple_faces' => 'danger',
                                                'copy_paste' => 'primary',
                                                'other' => 'secondary',
                                            ];
                                            $typeColor = $typeColors[$log->violation_type] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-soft-{{ $typeColor }} text-capitalize">
                                            {{ str_replace('_', ' ', $log->violation_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $log->description ?? '-' }}</td>
                                    <td>
                                        @php
                                            $severityColors = ['low' => 'success', 'medium' => 'warning', 'high' => 'danger'];
                                            $severityColor = $severityColors[$log->severity] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $severityColor }} text-capitalize">
                                            {{ $log->severity }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->snapshot_path)
                                            <a href="{{ route('proctoring.snapshot.view', $log->id) }}" 
                                               target="_blank"
                                               class="btn btn-sm btn-light-primary">
                                                <i class="ph ph-image me-1"></i>Lihat
                                            </a>
                                        @else
                                            <span class="text-muted f-12">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="ph ph-check-circle text-success f-48 mb-3 d-block"></i>
                    <h6>Tidak Ada Log Pelanggaran</h6>
                    <p class="mb-0">Peserta ini tidak melakukan pelanggaran selama ujian.</p>
                </div>
            @endif
        </div>
        
        @if($logs->hasPages())
            <div class="card-footer">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
