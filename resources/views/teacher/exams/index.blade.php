@extends('layouts.teacher')

@section('title', 'Kelola Ujian')
@section('page-title', 'Kelola Ujian')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="page-header-title">
                                <h5 class="m-b-10">Kelola Ujian</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph ph-house"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page">Ujian</li>
                            </ul>
                        </div>
                        <a href="{{ route('teacher.exams.create') }}" class="btn btn-primary">
                            <i class="ph ph-plus me-2"></i>Buat Ujian Baru
                        </a>
                    </div>
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
                            <p class="text-muted mb-1 small">Total Ujian</p>
                            <h3 class="mb-0">{{ $exams->total() }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-primary">
                            <i class="ph ph-file-text text-primary"></i>
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
                            <p class="text-muted mb-1 small">Sedang Berlangsung</p>
                            <h3 class="mb-0 text-success">{{ $exams->filter(fn($e) => $e->isActive())->count() }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-success">
                            <i class="ph ph-play-circle text-success"></i>
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
                            <p class="text-muted mb-1 small">Draft</p>
                            <h3 class="mb-0 text-warning">{{ $exams->where('status', 'draft')->count() }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-warning">
                            <i class="ph ph-pencil-simple text-warning"></i>
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
                            <p class="text-muted mb-1 small">Mata Pelajaran</p>
                            <h3 class="mb-0 text-info">{{ $courses->count() }}</h3>
                        </div>
                        <div class="avatar avatar-md bg-light-info">
                            <i class="ph ph-book text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('teacher.exams.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Cari Ujian</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="ph ph-magnifying-glass"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Nama ujian..."
                                   class="form-control border-start-0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="course" class="form-select">
                            <option value="">Semua Mapel</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Sedang Berlangsung</option>
                            <option value="ended" {{ request('status') === 'ended' ? 'selected' : '' }}>Berakhir</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ph ph-funnel me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Exams Table -->
    <div class="card table-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="ph ph-list-dashes me-2"></i>Daftar Ujian
            </h5>
            @if(request()->hasAny(['search', 'course', 'status']))
                <a href="{{ route('teacher.exams.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ph ph-x me-1"></i>Reset Filter
                </a>
            @endif
        </div>
        <div class="card-body p-0">
            @if($exams->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Ujian</th>
                                <th>Token</th>
                                <th>Jadwal</th>
                                <th>Soal</th>
                                <th>Peserta</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exams as $exam)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3" style="width: 45px; height: 45px; display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                                <i class="ph ph-file-text text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $exam->title }}</h6>
                                                <small class="text-muted">
                                                    <i class="ph ph-book-open me-1"></i>{{ $exam->course->name }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <code class="text-primary font-monospace" style="font-size: 0.85rem; letter-spacing: 1px;">{{ $exam->access_token }}</code>
                                            <button type="button" class="btn btn-sm btn-light p-1" 
                                                    onclick="copyToClipboard('{{ $exam->access_token }}')" title="Salin Token">
                                                <i class="ph ph-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @if($exam->type === 'flexible')
                                                <span class="badge bg-light-info">
                                                    <i class="ph ph-infinity me-1"></i>Fleksibel
                                                </span>
                                                <small class="text-muted d-block mt-1">
                                                    <i class="ph ph-clock me-1"></i>{{ $exam->duration }} menit
                                                </small>
                                            @elseif($exam->start_time)
                                                <span class="d-block">{{ $exam->start_time->format('d M Y') }}</span>
                                                <small class="text-muted">
                                                    <i class="ph ph-clock me-1"></i>{{ $exam->start_time->format('H:i') }} • {{ $exam->duration }} menit
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light-info">
                                            <i class="ph ph-list-numbers me-1"></i>{{ $exam->questions_count }} soal
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-medium">{{ $exam->attempts_count }}</span>
                                            <span class="text-muted">/ {{ $exam->course->students_count ?? 0 }}</span>
                                        </div>
                                        @if($exam->isActive())
                                            <a href="{{ route('teacher.monitor.index', $exam) }}" 
                                               class="badge bg-success text-white text-decoration-none">
                                                <i class="ph ph-broadcast me-1"></i>Monitor Live
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($exam->status === 'draft')
                                            <span class="badge bg-light-secondary">
                                                <i class="ph ph-pencil-simple me-1"></i>Draft
                                            </span>
                                        @elseif($exam->isActive())
                                            <span class="badge bg-success animate-pulse">
                                                <i class="ph ph-circle me-1" style="font-size: 8px;"></i>Berlangsung
                                            </span>
                                        @elseif(!$exam->hasStarted())
                                            <span class="badge bg-light-info">
                                                <i class="ph ph-calendar-dots me-1"></i>Terjadwal
                                            </span>
                                        @else
                                            <span class="badge bg-light-danger">
                                                <i class="ph ph-check-circle me-1"></i>Berakhir
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('teacher.exams.show', $exam) }}" 
                                               class="btn btn-sm btn-light-info" title="Detail">
                                                <i class="ph ph-eye"></i>
                                            </a>
                                            <a href="{{ route('teacher.exams.edit', $exam) }}" 
                                               class="btn btn-sm btn-light-warning" title="Edit">
                                                <i class="ph ph-pencil-simple"></i>
                                            </a>
                                            <a href="{{ route('teacher.questions.index', $exam) }}" 
                                               class="btn btn-sm btn-light-primary" title="Kelola Soal">
                                                <i class="ph ph-list-dashes"></i>
                                            </a>
                                            @if($exam->status === 'draft')
                                                <form action="{{ route('teacher.exams.publish', $exam) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Publish" onclick="return confirm('Publish ujian ini?')">
                                                        <i class="ph ph-paper-plane-tilt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('teacher.exams.destroy', $exam) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light-danger" title="Hapus" 
                                                        onclick="return confirm('Hapus ujian ini? Semua data akan dihapus permanen.')">
                                                    <i class="ph ph-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="ph ph-file-text"></i>
                    </div>
                    <h6>Belum Ada Ujian</h6>
                    <p class="text-muted mb-3">Mulai buat ujian pertama Anda untuk mata pelajaran yang Anda ajar.</p>
                    <a href="{{ route('teacher.exams.create') }}" class="btn btn-primary">
                        <i class="ph ph-plus me-2"></i>Buat Ujian Baru
                    </a>
                </div>
            @endif
        </div>
        @if($exams->hasPages())
            <div class="card-footer">
                {{ $exams->links() }}
            </div>
        @endif
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show temporary success feedback
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="ph ph-check text-success"></i>';
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                }, 1500);
            }).catch(err => {
                console.error('Failed to copy:', err);
            });
        }
    </script>
@endsection
