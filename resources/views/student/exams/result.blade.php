@extends('layouts.student')

@section('title', 'Hasil Ujian')
@section('page-title', 'Hasil Ujian')

@section('content')
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('student.exams.index') }}" class="btn btn-light btn-sm">
            <i class="ph ph-arrow-left me-1"></i>Kembali ke Daftar Ujian
        </a>
    </div>

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="ph ph-info me-1"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="ph ph-warning me-1"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Result Card -->
            <div class="card mb-4">
                @php
                    $bgClass = $attempt->is_passed ? 'bg-success' : 'bg-danger';
                @endphp
                <div class="card-header {{ $bgClass }} bg-opacity-100 bg-gradient text-white text-center py-5">
                    <div class="score-circle bg-white bg-opacity-15 border border-white border-opacity-25 rounded-4 d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 96px; height: 96px;">
                        <span class="f-36 f-w-600 text-white">{{ number_format($attempt->percentage, 0) }}%</span>
                    </div>
                    <h4 class="text-white mb-2">
                        {{ $attempt->is_passed ? '🎉 Selamat! Anda Lulus' : '😔 Maaf, Anda Tidak Lulus' }}
                    </h4>
                    <p class="text-white-75 mb-0">
                        Skor: {{ number_format($attempt->score, 2) }} / {{ $attempt->exam->total_points }}
                    </p>
                </div>
                
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-3 p-3" style="background: rgba(124, 58, 237, 0.1);">
                            <i class="ph ph-file-text f-24" style="color: #7c3aed;"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 f-w-600">{{ $attempt->exam->title }}</h5>
                            <small class="text-muted">{{ $attempt->exam->course?->name ?? 'Ujian Umum' }}</small>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <h4 class="mb-1">{{ $attempt->exam->question_count }}</h4>
                                <small class="text-muted">Total Soal</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <h4 class="mb-1">{{ $attempt->answers()->count() }}</h4>
                                <small class="text-muted">Dijawab</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3 text-center">
                                <h4 class="mb-1 text-success">{{ $attempt->answers()->where('is_correct', true)->count() }}</h4>
                                <small class="text-muted">Benar</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            @php
                                $isAutoSubmitted = (bool) $attempt->is_auto_submitted;
                                $hasCurrentThreshold = $maxViolations !== null;
                                $hasBoundedThreshold = $hasCurrentThreshold && $maxViolations > 0;
                                $reachedCurrentThreshold = $hasBoundedThreshold && $attempt->violation_count >= $maxViolations;
                                $autoSubmitBelowCurrentThreshold = $isAutoSubmitted && $hasBoundedThreshold && !$reachedCurrentThreshold;

                                if ($maxViolations === null) {
                                    $violationLabel = 'Pelanggaran';
                                } elseif ($maxViolations > 0) {
                                    if ($autoSubmitBelowCurrentThreshold) {
                                        $violationLabel = 'Pelanggaran (' . $attempt->violation_count . '; batas saat ini ' . $maxViolations . ')';
                                    } else {
                                        $violationLabel = 'Pelanggaran (' . $attempt->violation_count . '/' . $maxViolations . ')';
                                    }
                                } else {
                                    $violationLabel = 'Pelanggaran (Tanpa Batas)';
                                }
                            @endphp
                            <div class="bg-{{ $attempt->violation_count > 0 ? 'danger' : 'light' }} bg-opacity-10 rounded-3 p-3 text-center">
                                <h4 class="mb-1 {{ $attempt->violation_count > 0 ? 'text-danger' : '' }}">{{ $attempt->violation_count }}</h4>
                                <small class="text-muted">{{ $violationLabel }}</small>
                            </div>
                        </div>
                    </div>

                    @if($maxViolations !== null)
                        <div class="alert {{ $reachedCurrentThreshold ? 'alert-danger' : ($autoSubmitBelowCurrentThreshold ? 'alert-warning' : 'alert-light') }} mb-4">
                            <div class="f-14">
                                @if($maxViolations > 0)
                                    Batas pelanggaran ujian ini adalah <strong>{{ $maxViolations }}</strong>.
                                    @if($reachedCurrentThreshold)
                                        Attempt ini sudah <strong>mencapai batas</strong> pelanggaran.
                                    @elseif($autoSubmitBelowCurrentThreshold)
                                        Attempt ini tercatat <strong>auto-submit</strong>. Nilai batas saat ini lebih tinggi dari jumlah pelanggaran pada attempt ini, sehingga kemungkinan batas pelanggaran telah diperbarui setelah attempt selesai atau auto-submit terjadi karena kondisi lain (mis. waktu habis).
                                    @else
                                        Attempt ini belum mencapai batas pelanggaran.
                                    @endif
                                @else
                                    Ujian ini mengaktifkan pencatatan pelanggaran <strong>tanpa auto-submit berdasarkan jumlah pelanggaran</strong>.

                                    @if($isAutoSubmitted)
                                        Attempt ini tetap bisa berstatus auto-submit jika dikumpulkan otomatis karena kondisi lain, misalnya waktu ujian habis.
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted">Waktu Mulai:</small><br>
                            <span class="f-w-500">{{ $attempt->started_at->format('d M Y, H:i:s') }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Waktu Selesai:</small><br>
                            <span class="f-w-500">{{ $attempt->submitted_at->format('d M Y, H:i:s') }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Durasi Pengerjaan:</small><br>
                            <span class="f-w-500">{{ $attempt->exam->duration }} menit</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Metode Submit:</small><br>
                            <span class="f-w-500 {{ $attempt->is_auto_submitted ? 'text-danger' : '' }}">
                                {{ $attempt->is_auto_submitted ? 'Auto-submit' : 'Manual' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Answer Review (if enabled) -->
            @if($showAnswers)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pembahasan Jawaban</h5>
                    </div>
                    
                    <div class="card-body p-0">
                        @foreach($attempt->answers as $index => $answer)
                            <div class="p-4 border-bottom">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <span class="badge bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="badge {{ $answer->is_correct ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                        {{ $answer->is_correct ? 'Benar' : 'Salah' }}
                                        ({{ number_format($answer->points_earned, 1) }}/{{ $answer->question->points }} poin)
                                    </span>
                                </div>
                                
                                <p class="mb-3">{!! nl2br(e($answer->question->question)) !!}</p>
                                
                                @if($answer->question->isMultipleChoice())
                                    <div class="mb-3">
                                        @foreach($answer->question->options as $option)
                                            <div class="p-3 rounded-3 mb-2 {{ $option->is_correct ? 'bg-success bg-opacity-10 border border-success' : '' }}
                                                {{ $answer->selected_option_id === $option->id && !$option->is_correct ? 'bg-danger bg-opacity-10 border border-danger' : '' }}
                                                {{ !$option->is_correct && $answer->selected_option_id !== $option->id ? 'bg-light' : '' }}">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <span class="f-w-600 me-2">{{ $option->option_label }}.</span>
                                                        <span class="{{ $option->is_correct ? 'text-success f-w-500' : '' }}">{{ $option->option_text }}</span>
                                                    </div>
                                                    @if($option->is_correct)
                                                        <span class="text-success f-12"><i class="ph ph-check me-1"></i>Jawaban Benar</span>
                                                    @endif
                                                    @if($answer->selected_option_id === $option->id && !$option->is_correct)
                                                        <span class="text-danger f-12"><i class="ph ph-x me-1"></i>Jawaban Anda</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="bg-light rounded-3 p-3 mb-3">
                                        <small class="text-muted d-block mb-1">Jawaban Anda:</small>
                                        <span>{{ $answer->essay_answer ?: '(Tidak dijawab)' }}</span>
                                    </div>
                                    @if($answer->feedback)
                                        <div class="bg-info bg-opacity-10 rounded-3 p-3 mb-3">
                                            <small class="text-info f-w-500 d-block mb-1">Feedback dari Guru:</small>
                                            <span class="text-info">{{ $answer->feedback }}</span>
                                        </div>
                                    @endif
                                @endif
                                
                                @if($answer->question->explanation)
                                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                        <small class="text-warning f-w-500 d-block mb-1">Penjelasan:</small>
                                        <span>{{ $answer->question->explanation }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="alert alert-warning text-center">
                    <i class="ph ph-lock f-36 mb-2 d-block"></i>
                    <strong>Pembahasan jawaban tidak tersedia</strong>
                    <p class="mb-0 f-14">Pengaturan ujian ini tidak mengizinkan peserta melihat pembahasan.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
