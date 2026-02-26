@extends('layouts.teacher')

@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Welcome Banner -->
    <div class="card mb-4" style="background: linear-gradient(135deg, #082f1a 0%, #065f46 60%, #047857 100%); border: none; overflow: hidden; position: relative;">
        <div class="card-body p-4" style="position: relative; z-index: 1;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <div style="background: linear-gradient(135deg, #10b981, #34d399); border-radius: 14px;" class="p-3">
                            <i class="ph ph-chalkboard-teacher f-30 text-white"></i>
                        </div>
                        <div>
                            <p class="mb-1 f-14" style="color: rgba(255,255,255,0.5);">Selamat datang,</p>
                            <h3 class="mb-0 text-white f-w-600">{{ auth()->user()->name }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge px-3 py-2" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(4px); border: 1px solid rgba(255,255,255,0.06); color: rgba(255,255,255,0.7);">
                        <i class="ph ph-calendar-dots me-1"></i>{{ now()->locale('id')->translatedFormat('l, d M Y') }}
                    </span>
                </div>
            </div>
        </div>
        <div style="position: absolute; top: -20px; right: -10px; width: 140px; height: 140px; border-radius: 50%; background: rgba(16,185,129,0.08);"></div>
        <div style="position: absolute; bottom: -25px; right: 90px; width: 90px; height: 90px; border-radius: 50%; background: rgba(52,211,153,0.06);"></div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card" style="border-left: 3px solid #10b981;">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Mata Pelajaran</p>
                            <h3 class="mb-0" style="font-weight: 700;">{{ $stats['total_courses'] }}</h3>
                        </div>
                        <div class="avatar avatar-md" style="background: rgba(16,185,129,0.1); border-radius: 12px;">
                            <i class="ph ph-book" style="color: #10b981;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card" style="border-left: 3px solid #3b82f6;">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Total Siswa</p>
                            <h3 class="mb-0" style="font-weight: 700; color: #3b82f6;">{{ $stats['total_students'] }}</h3>
                        </div>
                        <div class="avatar avatar-md" style="background: rgba(59,130,246,0.1); border-radius: 12px;">
                            <i class="ph ph-users" style="color: #3b82f6;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card" style="border-left: 3px solid #f59e0b;">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Total Ujian</p>
                            <h3 class="mb-0" style="font-weight: 700; color: #f59e0b;">{{ $stats['total_exams'] }}</h3>
                        </div>
                        <div class="avatar avatar-md" style="background: rgba(245,158,11,0.1); border-radius: 12px;">
                            <i class="ph ph-file-text" style="color: #f59e0b;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card" style="border-left: 3px solid #ef4444;">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Ujian Aktif</p>
                            <h3 class="mb-0" style="font-weight: 700; color: #ef4444;">{{ $stats['active_exams'] }}</h3>
                        </div>
                        <div class="avatar avatar-md" style="background: rgba(239,68,68,0.1); border-radius: 12px;">
                            <i class="ph ph-broadcast" style="color: #ef4444;"></i>
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
                        <i class="ph ph-play-circle text-success me-2"></i>Ujian Berlangsung
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
                                        <i class="ph ph-file-text text-white"></i>
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
                            <i class="ph ph-calendar-x fs-1 d-block mb-2 opacity-50"></i>
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
                        <i class="ph ph-chart-line-up text-info me-2"></i>Aktivitas Terakhir
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
                            <i class="ph ph-clock fs-1 d-block mb-2 opacity-50"></i>
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
                <i class="ph ph-book text-primary me-2"></i>Mata Pelajaran Saya
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @forelse($courses as $course)
                    <div class="col-md-4">
                        <div class="card border h-100 hover-shadow">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-sm me-3" style="width: 45px; height: 45px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                        <i class="ph ph-book-open text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $course->name }}</h6>
                                        <small class="text-muted">{{ $course->code }}</small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between text-muted small">
                                    <span><i class="ph ph-users me-1"></i>{{ $course->students_count }} siswa</span>
                                    <span><i class="ph ph-file-text me-1"></i>{{ $course->exams_count }} ujian</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="ph ph-folder-open fs-1 d-block mb-2 opacity-50"></i>
                        Belum ada mata pelajaran yang ditugaskan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .stats-card {
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.08);
    }
    .hover-shadow {
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
</style>
@endpush
