@extends('layouts.teacher')

@section('title', 'Detail Ujian')
@section('page-title', 'Detail Ujian')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="page-header-title">
                                <h5 class="m-b-10">{{ $exam->title }}</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph ph-house"></i></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Ujian</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($exam->title, 30) }}</li>
                            </ul>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('teacher.exams.index') }}" class="btn btn-outline-secondary">
                                <i class="ph ph-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-2">{{ $exam->title }}</h4>
                            <p class="text-muted mb-2">
                                <i class="ph ph-book me-2"></i>{{ $exam->course->name }}
                            </p>
                            <div class="d-flex gap-2 align-items-center">
                                @if($exam->status === 'draft')
                                    <span class="badge bg-light-secondary">
                                        <i class="ph ph-file me-1"></i>Draft
                                    </span>
                                @elseif($exam->status === 'published')
                                    <span class="badge bg-light-success">
                                        <i class="ph ph-check-circle me-1"></i>Dipublikasikan
                                    </span>
                                @elseif($exam->status === 'ongoing')
                                    <span class="badge bg-light-info">
                                        <i class="ph ph-clock me-1"></i>Sedang Berlangsung
                                    </span>
                                @elseif($exam->status === 'completed')
                                    <span class="badge bg-light-warning">
                                        <i class="ph ph-flag-checkered me-1"></i>Selesai
                                    </span>
                                @endif

                                @if($exam->type === 'scheduled')
                                    <span class="badge bg-light-primary">
                                        <i class="ph ph-calendar-dots me-1"></i>Terjadwal
                                    </span>
                                @else
                                    <span class="badge bg-light-info">
                                        <i class="ph ph-infinity me-1"></i>Fleksibel
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('teacher.exams.edit', $exam) }}" class="btn btn-warning">
                                <i class="ph ph-pencil-simple me-2"></i>Edit
                            </a>
                            @if($exam->status === 'draft')
                                <form action="{{ route('teacher.exams.publish', $exam) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" 
                                            onclick="return confirm('Publikasikan ujian ini? Siswa akan dapat melihat ujian ini.')">
                                        <i class="ph ph-paper-plane-tilt me-2"></i>Publikasikan
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('teacher.exams.duplicate', $exam) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="ph ph-copy me-2"></i>Duplikat
                                </button>
                            </form>
                            <form action="{{ route('teacher.exams.destroy', $exam) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Hapus ujian ini? Semua data soal dan jawaban akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.')">
                                    <i class="ph ph-trash me-2"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </div>
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
                            <div class="avatar avatar-sm bg-light-primary">
                                <i class="ph ph-clipboard-text"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Soal</h6>
                            <h4 class="mb-0 text-primary">{{ $exam->questions->count() }}</h4>
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
                            <div class="avatar avatar-sm bg-light-success">
                                <i class="ph ph-users"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Peserta</h6>
                            <h4 class="mb-0 text-success">{{ $statistics['total_participants'] ?? 0 }}</h4>
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
                            <div class="avatar avatar-sm bg-light-info">
                                <i class="ph ph-check-circle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Selesai</h6>
                            <h4 class="mb-0 text-info">{{ $statistics['completed_attempts'] ?? 0 }}</h4>
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
                            <div class="avatar avatar-sm bg-light-warning">
                                <i class="ph ph-star"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Poin</h6>
                            <h4 class="mb-0 text-warning">{{ $exam->questions->sum('points') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ph ph-lightning text-warning me-2"></i>Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('teacher.questions.index', $exam) }}" class="btn btn-outline-primary w-100">
                                <i class="ph ph-clipboard-text me-2"></i>Kelola Soal
                                <span class="badge bg-primary ms-2">{{ $exam->questions->count() }}</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('teacher.monitor.index', $exam) }}" class="btn btn-outline-info w-100">
                                <i class="ph ph-monitor me-2"></i>Monitor Ujian
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('teacher.exams.results', $exam) }}" class="btn btn-outline-success w-100">
                                <i class="ph ph-chart-bar me-2"></i>Lihat Hasil
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('teacher.exams.export', $exam) }}" class="btn btn-outline-secondary w-100">
                                <i class="ph ph-download-simple me-2"></i>Export Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Details -->
    <div class="row">
        <div class="col-md-8">
            <!-- Exam Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ph ph-info text-primary me-2"></i>Informasi Ujian</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted small">Mata Pelajaran</label>
                            <p class="mb-0 fw-semibold">{{ $exam->course->name }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Tipe Ujian</label>
                            <p class="mb-0 fw-semibold">
                                @if($exam->type === 'scheduled')
                                    Terjadwal
                                @else
                                    Fleksibel
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Durasi</label>
                            <p class="mb-0 fw-semibold">{{ $exam->duration }} menit</p>
                        </div>
                    </div>

                    @if($exam->type === 'scheduled')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Waktu Mulai</label>
                                <p class="mb-0 fw-semibold">
                                    <i class="ph ph-calendar-dots me-1"></i>
                                    {{ $exam->start_time ? $exam->start_time->format('d M Y, H:i') : '-' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Waktu Selesai</label>
                                <p class="mb-0 fw-semibold">
                                    <i class="ph ph-calendar-dots me-1"></i>
                                    {{ $exam->end_time ? $exam->end_time->format('d M Y, H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($exam->description)
                        <div class="row">
                            <div class="col-12">
                                <label class="text-muted small">Deskripsi</label>
                                <p class="mb-0">{{ $exam->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Questions Preview -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="ph ph-list-numbers text-success me-2"></i>Soal</h5>
                        <a href="{{ route('teacher.questions.index', $exam) }}" class="btn btn-sm btn-primary">
                            <i class="ph ph-pencil-simple me-1"></i>Kelola Soal
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($exam->questions->isEmpty())
                        <div class="text-center py-4">
                            <i class="ph ph-clipboard-text text-muted mb-3" style="font-size: 48px; opacity: 0.3;"></i>
                            <p class="text-muted mb-3">Belum ada soal untuk ujian ini</p>
                            <a href="{{ route('teacher.questions.index', $exam) }}" class="btn btn-sm btn-primary">
                                <i class="ph ph-plus me-1"></i>Tambah Soal
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">No</th>
                                        <th>Pertanyaan</th>
                                        <th width="120">Tipe</th>
                                        <th width="80" class="text-center">Poin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exam->questions->take(5) as $index => $question)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ Str::limit($question->question, 80) }}</td>
                                            <td>
                                                @if($question->type === 'multiple_choice')
                                                    <span class="badge bg-light-success">
                                                        <i class="ph ph-check-square me-1"></i>Pilihan Ganda
                                                    </span>
                                                @else
                                                    <span class="badge bg-light-info">
                                                        <i class="ph ph-text-aa me-1"></i>Essay
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light-warning">{{ $question->points }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($exam->questions->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('teacher.questions.index', $exam) }}" class="text-muted">
                                    Lihat semua {{ $exam->questions->count() }} soal <i class="ph ph-arrow-right ms-1"></i>
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Proctoring Settings -->
            @if($exam->settings)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ph ph-shield-check text-info me-2"></i>Pengaturan Proctoring</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Webcam Monitor</span>
                            @if($exam->settings->webcam_enabled)
                                <span class="badge bg-light-success">Aktif</span>
                            @else
                                <span class="badge bg-light-secondary">Nonaktif</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Browser Lock</span>
                            @if($exam->settings->browser_lock_enabled)
                                <span class="badge bg-light-success">Aktif</span>
                            @else
                                <span class="badge bg-light-secondary">Nonaktif</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Tab Switch Detection</span>
                            @if($exam->settings->tab_switch_detection)
                                <span class="badge bg-light-success">Aktif</span>
                            @else
                                <span class="badge bg-light-secondary">Nonaktif</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Blokir Kecurangan</span>
                            @if($exam->settings->block_keyboard_shortcuts)
                                <span class="badge bg-light-success">Aktif</span>
                            @else
                                <span class="badge bg-light-secondary">Nonaktif</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Max Tab Switch</span>
                            <span class="badge bg-light-warning">{{ $exam->settings->max_tab_switches ?? 0 }}x</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Display Settings -->
            @if($exam->settings)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ph ph-eye text-success me-2"></i>Pengaturan Tampilan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Acak Soal</span>
                            @if($exam->settings->shuffle_questions)
                                <span class="badge bg-light-success">Ya</span>
                            @else
                                <span class="badge bg-light-secondary">Tidak</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Acak Opsi</span>
                            @if($exam->settings->shuffle_options)
                                <span class="badge bg-light-success">Ya</span>
                            @else
                                <span class="badge bg-light-secondary">Tidak</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Tampilkan Jawaban</span>
                            @if($exam->settings->show_correct_answers)
                                <span class="badge bg-light-success">Ya</span>
                            @else
                                <span class="badge bg-light-secondary">Tidak</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Tampilkan Nilai</span>
                            @if($exam->settings->show_score)
                                <span class="badge bg-light-success">Ya</span>
                            @else
                                <span class="badge bg-light-secondary">Tidak</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Attempts Settings -->
            @if($exam->settings)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ph ph-repeat text-warning me-2"></i>Percobaan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Maks. Percobaan</span>
                            @if($exam->settings->max_attempts)
                                <span class="badge bg-light-warning">{{ $exam->settings->max_attempts }}x</span>
                            @else
                                <span class="badge bg-light-success">Unlimited</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Nilai yang Digunakan</span>
                            <span class="badge bg-light-info">
                                @if($exam->settings->grade_method === 'highest')
                                    Tertinggi
                                @elseif($exam->settings->grade_method === 'average')
                                    Rata-rata
                                @else
                                    Terakhir
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Access Token -->
            <div class="card mb-4">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <h5 class="mb-0 text-white"><i class="ph ph-key me-2"></i>Token Akses Ujian</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Bagikan token ini kepada siswa untuk mengakses ujian.</p>
                    
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg text-center font-monospace" 
                               value="{{ $exam->access_token }}" id="accessToken" readonly 
                               style="letter-spacing: 2px; font-weight: bold;">
                        <button class="btn btn-primary" type="button" onclick="copyToken()" title="Salin Token">
                            <i class="ph ph-copy"></i>
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <form action="{{ route('teacher.exams.regenerate-token', $exam) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning w-100" 
                                    onclick="return confirm('Generate token baru? Token lama tidak akan bisa digunakan lagi.')">
                                <i class="ph ph-arrows-clockwise me-2"></i>Generate Token Baru
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="ph ph-warning me-2"></i>Zona Berbahaya</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Hapus ujian ini secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                    <form action="{{ route('teacher.exams.destroy', $exam) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" 
                                onclick="return confirm('PERINGATAN: Anda akan menghapus ujian \&quot;{{ $exam->title }}\&quot; beserta semua soal, jawaban, dan data percobaan siswa. Tindakan ini TIDAK DAPAT dibatalkan. Apakah Anda yakin?')">
                            <i class="ph ph-trash me-2"></i>Hapus Ujian Ini
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToken() {
            const tokenInput = document.getElementById('accessToken');
            tokenInput.select();
            tokenInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(tokenInput.value).then(() => {
                // Show tooltip or toast
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="ph ph-check"></i>';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                }, 2000);
            });
        }
    </script>
@endsection
