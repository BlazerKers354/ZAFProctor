@extends('layouts.teacher')

@section('title', 'Detail Peserta')
@section('page-title', 'Detail Peserta')

@section('content')
    <div class="card zaf-hero mb-4 zaf-reveal">
        <div class="card-body p-4" style="position: relative; z-index: 1;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <p class="hero-kicker mb-2">Invigilation Detail</p>
                    <h5 class="hero-title mb-1">Detail Peserta: {{ $attempt->user->name }}</h5>
                    <p class="mb-0" style="color: rgba(248,250,252,0.82);">Pantau progres jawaban, snapshot kamera, dan jejak pelanggaran peserta.</p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($attempt->isSubmitted())
                        <span class="hero-chip"><i class="ph ph-check-circle me-1"></i>Selesai</span>
                    @elseif($attempt->isInProgress())
                        <span class="hero-chip"><i class="ph ph-pencil-simple me-1"></i>Mengerjakan</span>
                    @else
                        <span class="hero-chip"><i class="ph ph-hourglass-medium me-1"></i>Belum Mulai</span>
                    @endif
                    <a href="{{ route('teacher.monitor.logs', [$exam, $attempt]) }}" class="btn btn-light btn-sm">
                        <i class="ph ph-list-checks me-2"></i>Lihat Log
                    </a>
                    <a href="{{ route('teacher.monitor.index', $exam) }}" class="btn btn-light btn-sm">
                        <i class="ph ph-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row zaf-reveal">
        <!-- User Info -->
        <div class="col-lg-4">
            <div class="card mb-4 zaf-reveal">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ph ph-user me-2"></i>Info Peserta
                    </h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ $attempt->user->avatar_url }}" alt="{{ $attempt->user->name }}" 
                         class="rounded-circle mb-3" width="80" height="80">
                    <h5 class="mb-1">{{ $attempt->user->name }}</h5>
                    <p class="text-muted mb-3">{{ $attempt->user->student_id ?? $attempt->user->email }}</p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        @if($attempt->isSubmitted())
                            <span class="badge bg-success px-3 py-2">Selesai</span>
                        @elseif($attempt->isInProgress())
                            <span class="badge bg-warning px-3 py-2">Mengerjakan</span>
                        @else
                            <span class="badge bg-secondary px-3 py-2">Belum Mulai</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Attempt Stats -->
            <div class="card zaf-reveal">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ph ph-chart-line-up me-2"></i>Statistik
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Waktu Mulai</span>
                            <span class="fw-medium">{{ $attempt->started_at?->format('H:i:s') ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Waktu Selesai</span>
                            <span class="fw-medium">{{ $attempt->submitted_at?->format('H:i:s') ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Durasi</span>
                            <span class="fw-medium">
                                @if($attempt->started_at && $attempt->submitted_at)
                                    {{ $attempt->started_at->diffForHumans($attempt->submitted_at, true) }}
                                @elseif($attempt->started_at)
                                    {{ $attempt->started_at->diffForHumans(now(), true) }} (berlangsung)
                                @else
                                    -
                                @endif
                            </span>
                        </li>
                        @if($attempt->isSubmitted())
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Skor</span>
                                <span class="fw-medium {{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($attempt->percentage, 1) }}%
                                </span>
                            </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">IP Address</span>
                            <span class="fw-medium font-monospace f-12">{{ $attempt->ip_address ?? '-' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Violation Summary -->
            <div class="card mb-4 zaf-reveal">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ph ph-warning text-danger me-2"></i>Ringkasan Pelanggaran
                    </h5>
                    <span class="badge bg-{{ $attempt->violation_count > 0 ? 'danger' : 'success' }} px-3">
                        {{ $attempt->violation_count }} Pelanggaran
                    </span>
                </div>
                <div class="card-body">
                    @if(isset($violationSummary['by_type']) && count($violationSummary['by_type']) > 0)
                        <div class="row g-3">
                            @foreach($violationSummary['by_type'] as $type => $count)
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center" style="background: var(--zaf-accent-soft); border-color: rgba(148, 163, 184, 0.32) !important;">
                                        <h4 class="mb-1" style="color: var(--zaf-accent);">{{ $count }}</h4>
                                        <small class="text-muted text-capitalize">{{ str_replace('_', ' ', $type) }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <hr class="my-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted d-block">Total</small>
                                <span class="fw-bold">{{ $violationSummary['total'] ?? 0 }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Reviewed</small>
                                <span class="fw-bold text-success">{{ $violationSummary['reviewed'] ?? 0 }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Pending</small>
                                <span class="fw-bold text-warning">{{ $violationSummary['pending_review'] ?? 0 }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="ph ph-check-circle text-success f-36 mb-2 d-block"></i>
                            <p class="mb-0">Tidak ada pelanggaran tercatat</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Camera Snapshots -->
            <div class="card mb-4 zaf-reveal">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ph ph-camera me-2"></i>Snapshot Kamera
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($snapshots) && count($snapshots) > 0)
                        <div class="row g-3">
                            @foreach(array_slice($snapshots, 0, 8) as $snapshot)
                                <div class="col-6 col-md-3">
                                    <div class="position-relative">
                                        <img src="{{ is_array($snapshot) ? $snapshot['url'] : route('proctoring.snapshot.view', $snapshot->id) }}" 
                                             alt="Snapshot" class="img-fluid rounded" style="aspect-ratio: 4/3; object-fit: cover; width: 100%;"
                                             onerror="this.style.display='none'">
                                        <small class="position-absolute bottom-0 start-0 bg-dark text-white px-2 py-1 rounded-end f-10">
                                            {{ is_array($snapshot) ? $snapshot['timestamp'] : $snapshot->created_at->format('H:i:s') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="ph ph-video-camera-slash f-36 mb-2 d-block opacity-50"></i>
                            <p class="mb-0">Tidak ada snapshot tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Answers Preview -->
            <div class="card zaf-reveal">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ph ph-list-numbers me-2"></i>Jawaban Peserta
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Soal</th>
                                    <th>Jawaban</th>
                                    <th>Status</th>
                                    <th>Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attempt->answers as $index => $answer)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                                {{ Str::limit($answer->question->question, 50) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($answer->selectedOption)
                                                {{ $answer->selectedOption->option_label }}. {{ Str::limit($answer->selectedOption->option_text, 30) }}
                                            @elseif($answer->essay_answer)
                                                <span class="text-muted">{{ Str::limit($answer->essay_answer, 50) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($answer->is_correct === true)
                                                <span class="badge badge-soft-success">Benar</span>
                                            @elseif($answer->is_correct === false)
                                                <span class="badge badge-soft-danger">Salah</span>
                                            @else
                                                <span class="badge badge-soft-secondary">Belum dinilai</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $answer->points_earned ?? 0 }}/{{ $answer->question->points }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Belum ada jawaban
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
@endsection
