@extends('layouts.teacher')

@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Dashboard Guru</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Mata Pelajaran</p>
                            <h3 class="mb-0">{{ $stats['total_courses'] }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-primary">
                            <i class="ph-duotone ph-books text-primary"></i>
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
                            <p class="text-muted mb-1 small">Total Siswa</p>
                            <h3 class="mb-0 text-info">{{ $stats['total_students'] }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-info">
                            <i class="ph-duotone ph-users text-info"></i>
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
                            <p class="text-muted mb-1 small">Total Ujian</p>
                            <h3 class="mb-0 text-success">{{ $stats['total_exams'] }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-success">
                            <i class="ph-duotone ph-exam text-success"></i>
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
                            <p class="text-muted mb-1 small">Ujian Aktif</p>
                            <h3 class="mb-0 text-warning">{{ $stats['active_exams'] }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-warning">
                            <i class="ph-duotone ph-broadcast text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Active Exams with Monitoring -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ph-duotone ph-play-circle text-success me-2"></i>Ujian Berlangsung
                    </h5>
                    <a href="{{ route('teacher.exams.create') }}" class="btn btn-sm btn-primary">
                        <i class="ph ph-plus me-1"></i>Buat Ujian
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($activeExams as $exam)
                        <div class="p-3 border-bottom">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3" style="width: 45px; height: 45px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                        <i class="ph-duotone ph-exam text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $exam->title }}</h6>
                                        <small class="text-muted">
                                            <i class="ph ph-book-open me-1"></i>{{ $exam->course->name }} • {{ $exam->attempts_count }} peserta
                                        </small>
                                    </div>
                                </div>
                                <a href="{{ route('teacher.monitor.index', $exam) }}" class="btn btn-success btn-sm">
                                    <i class="ph ph-broadcast me-1"></i>Monitor
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="ph-duotone ph-calendar-x fs-1 d-block mb-2 opacity-50"></i>
                            Tidak ada ujian aktif saat ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Attempts -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ph-duotone ph-activity text-info me-2"></i>Aktivitas Terakhir
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 350px; overflow-y: auto;">
                    @forelse($recentAttempts as $attempt)
                        <div class="p-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <img class="rounded-circle me-3" src="{{ $attempt->user->avatar_url }}" alt="" width="40" height="40">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $attempt->user->name }}</h6>
                                    <small class="text-muted">{{ $attempt->exam->title }}</small>
                                </div>
                                <div class="text-end">
                                    @if($attempt->status === 'in_progress')
                                        <span class="badge bg-warning">Mengerjakan</span>
                                    @elseif($attempt->status === 'submitted' || $attempt->status === 'graded')
                                        <span class="badge {{ $attempt->is_passed ? 'bg-success' : 'bg-danger' }}">
                                            {{ number_format($attempt->percentage, 1) }}%
                                        </span>
                                    @endif
                                    @if($attempt->violation_count > 0)
                                        <div class="small text-danger">
                                            <i class="ph ph-warning me-1"></i>{{ $attempt->violation_count }} pelanggaran
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            <i class="ph-duotone ph-clock-countdown fs-1 d-block mb-2 opacity-50"></i>
                            Belum ada aktivitas.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Overview -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="ph-duotone ph-books text-primary me-2"></i>Mata Pelajaran Saya
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @forelse($courses as $course)
                    <div class="col-md-4">
                        <div class="card border h-100 hover-shadow">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-sm me-3" style="width: 45px; height: 45px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                                        <i class="ph-duotone ph-book-open text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $course->name }}</h6>
                                        <small class="text-muted">{{ $course->code }}</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between text-muted small">
                                    <span><i class="ph ph-users me-1"></i>{{ $course->students_count }} siswa</span>
                                    <span><i class="ph ph-exam me-1"></i>{{ $course->exams_count }} ujian</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="ph-duotone ph-folder-open fs-1 d-block mb-2 opacity-50"></i>
                        Belum ada mata pelajaran yang ditugaskan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
