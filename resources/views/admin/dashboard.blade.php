@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Welcome Banner -->
    <div class="card zaf-hero mb-4 zaf-reveal">
        <div class="card-body p-4" style="position: relative; z-index: 1;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <div style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.92), rgba(14, 165, 233, 0.85)); border-radius: 14px;" class="p-3 shadow-lg">
                            <i class="ph ph-chart-pie-slice f-30 text-white"></i>
                        </div>
                        <div>
                            <p class="hero-kicker mb-2">Panel Administrasi</p>
                            <h3 class="hero-title mb-0 text-white f-w-600">Dashboard Administrator</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="hero-chip">
                        <i class="ph ph-calendar-dots me-1"></i>{{ now()->locale('id')->translatedFormat('l, d M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Stats -->
    <div class="row zaf-reveal">
        <div class="col-md-6 col-xl-3">
            <div class="card stats-card" style="border-left: 3px solid #3b82f6;">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="stats-label mb-2">Total Pengguna</p>
                            <h3 class="stats-value">{{ number_format($stats['total_users']) }}</h3>
                        </div>
                        <div class="stats-icon" style="background: rgba(59,130,246,0.08);">
                            <i class="ph ph-users" style="color: #3b82f6;"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <span class="badge badge-soft-success me-2">
                            <i class="ph ph-trend-up me-1"></i>Aktif
                        </span>
                        <span class="text-muted f-12">{{ $stats['total_users'] }} terdaftar</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stats-card" style="border-left: 3px solid #10b981;">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="stats-label mb-2">Total Mata Pelajaran</p>
                            <h3 class="stats-value">{{ number_format($stats['total_courses']) }}</h3>
                        </div>
                        <div class="stats-icon" style="background: rgba(16,185,129,0.08);">
                            <i class="ph ph-book" style="color: #10b981;"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <span class="badge badge-soft-primary me-2">
                            <i class="ph ph-book-open me-1"></i>Tersedia
                        </span>
                        <span class="text-muted f-12">untuk semua kelas</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stats-card" style="border-left: 3px solid #f59e0b;">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="stats-label mb-2">Total Ujian</p>
                            <h3 class="stats-value">{{ number_format($stats['total_exams']) }}</h3>
                        </div>
                        <div class="stats-icon" style="background: rgba(245,158,11,0.08);">
                            <i class="ph ph-file-text" style="color: #f59e0b;"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <span class="badge badge-soft-warning me-2">
                            <i class="ph ph-clock me-1"></i>{{ $stats['active_exams'] ?? 0 }}
                        </span>
                        <span class="text-muted f-12">ujian aktif</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stats-card" style="border-left: 3px solid #06b6d4;">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="stats-label mb-2">Ujian Selesai</p>
                            <h3 class="stats-value">{{ number_format($stats['completed_attempts']) }}</h3>
                        </div>
                        <div class="stats-icon" style="background: rgba(6,182,212,0.08);">
                            <i class="ph ph-check-circle" style="color: #06b6d4;"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <span class="badge badge-soft-info me-2">
                            <i class="ph ph-chart-line-up me-1"></i>Selesai
                        </span>
                        <span class="text-muted f-12">total percobaan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Stats Cards -->
    <div class="row zaf-reveal">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg rounded" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="ph ph-graduation-cap text-white f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-muted f-12">Total Siswa</h6>
                            <h4 class="mb-0 f-w-700">{{ number_format($stats['total_students']) }}</h4>
                        </div>
                        <div class="text-end">
                            <span class="badge badge-soft-primary">Siswa</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg rounded" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="ph ph-chalkboard-teacher text-white f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-muted f-12">Total Guru</h6>
                            <h4 class="mb-0 f-w-700">{{ number_format($stats['total_teachers']) }}</h4>
                        </div>
                        <div class="text-end">
                            <span class="badge badge-soft-success">Guru</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-lg rounded" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="ph ph-clock text-white f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 text-muted f-12">Ujian Aktif</h6>
                            <h4 class="mb-0 f-w-700">{{ number_format($stats['active_exams'] ?? 0) }}</h4>
                        </div>
                        <div class="text-end">
                            <span class="badge badge-soft-warning">Berlangsung</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row zaf-reveal">
        <!-- Recent Users -->
        <div class="col-xl-6">
            <div class="card table-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <i class="ph ph-users me-2"></i>User Terbaru
                    </h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light-primary btn-sm">
                        Lihat Semua <i class="ph ph-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Pengguna</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar avatar-sm avatar-circle me-3">
                                                <div>
                                                    <h6 class="mb-0 f-14">{{ $user->name }}</h6>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->role->name === 'admin')
                                                <span class="badge badge-soft-danger">Admin</span>
                                            @elseif($user->role->name === 'teacher')
                                                <span class="badge badge-soft-success">Guru</span>
                                            @else
                                                <span class="badge badge-soft-primary">Siswa</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge badge-soft-success">
                                                    <i class="ph ph-check-circle me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="badge badge-soft-secondary">
                                                    <i class="ph ph-minus-circle me-1"></i>Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            Belum ada pengguna terdaftar
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Exams -->
        <div class="col-xl-6">
            <div class="card table-card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <i class="ph ph-file-text me-2"></i>Ujian Terbaru
                    </h5>

                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Ujian</th>
                                    <th>Pembuat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentExams as $exam)
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="mb-0 f-14">{{ Str::limit($exam->title, 25) }}</h6>
                                                <small class="text-muted">{{ $exam->course?->name ?? '-' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $exam->creator?->name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            @if($exam->status === 'draft')
                                                <span class="badge badge-soft-secondary">Draft</span>
                                            @elseif($exam->status === 'published')
                                                <span class="badge badge-soft-success">Published</span>
                                            @elseif($exam->status === 'ongoing')
                                                <span class="badge badge-soft-warning">Berlangsung</span>
                                            @else
                                                <span class="badge badge-soft-info">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            Belum ada ujian dibuat
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <h5 class="mb-3"><i class="ph ph-lightning me-2"></i>Aksi Cepat</h5>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.users.create') }}" class="card quick-action-card text-decoration-none">
                <div class="card-body text-center py-4">
                    <div class="avatar avatar-xl rounded mx-auto mb-3" style="background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center;">
                        <i class="ph ph-user-plus text-primary f-30"></i>
                    </div>
                    <h6 class="mb-1">Tambah Pengguna</h6>
                    <small class="text-muted">Daftarkan user baru</small>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.classes.create') }}" class="card quick-action-card text-decoration-none">
                <div class="card-body text-center py-4">
                    <div class="avatar avatar-xl rounded mx-auto mb-3" style="background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center;">
                        <i class="ph ph-chalkboard text-success f-30"></i>
                    </div>
                    <h6 class="mb-1">Tambah Kelas</h6>
                    <small class="text-muted">Buat kelas baru</small>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.courses.create') }}" class="card quick-action-card text-decoration-none">
                <div class="card-body text-center py-4">
                    <div class="avatar avatar-xl rounded mx-auto mb-3" style="background: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center;">
                        <i class="ph ph-book-bookmark text-warning f-30"></i>
                    </div>
                    <h6 class="mb-1">Tambah Mapel</h6>
                    <small class="text-muted">Mata pelajaran baru</small>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.users.pending') }}" class="card quick-action-card text-decoration-none">
                <div class="card-body text-center py-4">
                    <div class="avatar avatar-xl rounded mx-auto mb-3" style="background: rgba(239, 68, 68, 0.1); display: flex; align-items: center; justify-content: center;">
                        <i class="ph ph-user-check text-danger f-30"></i>
                    </div>
                    <h6 class="mb-1">Approval User</h6>
                    <small class="text-muted">Review pendaftaran</small>
                </div>
            </a>
        </div>
    </div>
@endsection
