@extends('layouts.teacher')

@section('title', 'Penilaian Manual')
@section('page-title', 'Penilaian Manual')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="page-header-title">
                                <h5 class="m-b-10">Penilaian Manual</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Ujian</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.show', $exam) }}">{{ Str::limit($exam->title, 20) }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.results', $exam) }}">Hasil</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Penilaian</li>
                            </ul>
                        </div>
                        <a href="{{ route('teacher.exams.results', $exam) }}" class="btn btn-outline-secondary">
                            <i class="ph ph-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3 bg-light-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <span class="fw-bold fs-4 text-primary">{{ strtoupper(substr($attempt->student->name ?? 'U', 0, 1)) }}</span>
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $attempt->student->name ?? 'Unknown' }}</h5>
                            <p class="text-muted mb-0">
                                <span class="me-3"><i class="ph ph-identification-card me-1"></i>{{ $attempt->student->nis ?? '-' }}</span>
                                <span><i class="ph ph-graduation-cap me-1"></i>{{ $attempt->student->schoolClass?->name ?? '-' }}</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end gap-3">
                        <div class="text-center">
                            <small class="text-muted d-block">Waktu Mulai</small>
                            <span class="fw-medium">{{ $attempt->started_at?->format('d/m/Y H:i') ?? '-' }}</span>
                        </div>
                        <div class="text-center">
                            <small class="text-muted d-block">Waktu Selesai</small>
                            <span class="fw-medium">{{ $attempt->finished_at?->format('d/m/Y H:i') ?? '-' }}</span>
                        </div>
                        <div class="text-center">
                            <small class="text-muted d-block">Status</small>
                            @switch($attempt->status)
                                @case('completed')
                                    <span class="badge bg-success">Selesai</span>
                                    @break
                                @case('in_progress')
                                    <span class="badge bg-info">Berlangsung</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ ucfirst($attempt->status) }}</span>
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('teacher.exams.submit-grade', $attempt) }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Questions and Answers -->
                @foreach($exam->questions as $index => $question)
                    @php
                        $answer = $attempt->answers->firstWhere('question_id', $question->id);
                    @endphp
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                    <span class="badge bg-light-info">{{ ucfirst($question->type) }}</span>
                                    <span class="badge bg-light-warning ms-2">{{ $question->points ?? 1 }} poin</span>
                                </h6>
                                @if($answer)
                                    @if($answer->is_correct)
                                        <span class="badge bg-success"><i class="ph ph-check me-1"></i>Benar</span>
                                    @else
                                        <span class="badge bg-danger"><i class="ph ph-x me-1"></i>Salah</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Tidak dijawab</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Question Text -->
                            <div class="mb-4">
                                <div class="question-text">{!! $question->question_text !!}</div>
                            </div>

                            <!-- Options / Answer -->
                            @if($question->type === 'multiple_choice' || $question->type === 'single_choice')
                                <div class="mb-3">
                                    <label class="form-label text-muted">Opsi Jawaban:</label>
                                    @foreach($question->options as $option)
                                        @php
                                            $isSelected = $answer && $answer->selected_option_id == $option->id;
                                            $isCorrect = $option->is_correct;
                                        @endphp
                                        <div class="p-2 mb-2 rounded {{ $isCorrect ? 'bg-light-success' : ($isSelected && !$isCorrect ? 'bg-light-danger' : 'bg-light') }}">
                                            <div class="d-flex align-items-center">
                                                @if($isSelected)
                                                    <i class="ph-fill ph-check-circle text-{{ $isCorrect ? 'success' : 'danger' }} me-2"></i>
                                                @elseif($isCorrect)
                                                    <i class="ph-fill ph-check-circle text-success me-2"></i>
                                                @else
                                                    <i class="ph ph-circle me-2 text-muted"></i>
                                                @endif
                                                <span>{{ $option->option_text }}</span>
                                                @if($isSelected)
                                                    <span class="badge bg-primary ms-2">Jawaban Siswa</span>
                                                @endif
                                                @if($isCorrect)
                                                    <span class="badge bg-success ms-2">Kunci Jawaban</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question->type === 'essay')
                                <div class="mb-3">
                                    <label class="form-label text-muted">Jawaban Siswa:</label>
                                    <div class="p-3 bg-light rounded">
                                        @if($answer && $answer->essay_answer)
                                            {!! nl2br(e($answer->essay_answer)) !!}
                                        @else
                                            <em class="text-muted">Tidak ada jawaban</em>
                                        @endif
                                    </div>
                                </div>
                                @if($question->answer_key)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Kunci Jawaban:</label>
                                        <div class="p-3 bg-light-success rounded">
                                            {!! nl2br(e($question->answer_key)) !!}
                                        </div>
                                    </div>
                                @endif
                            @elseif($question->type === 'true_false')
                                <div class="mb-3">
                                    @php
                                        $studentAnswer = $answer?->essay_answer;
                                        $correctAnswer = $question->correct_answer ?? 'true';
                                    @endphp
                                    <label class="form-label text-muted">Jawaban:</label>
                                    <div class="d-flex gap-3">
                                        <div class="p-2 px-4 rounded {{ $studentAnswer === 'true' ? ($studentAnswer === $correctAnswer ? 'bg-light-success' : 'bg-light-danger') : ($correctAnswer === 'true' ? 'bg-light-success' : 'bg-light') }}">
                                            Benar
                                            @if($studentAnswer === 'true')
                                                <span class="badge bg-primary ms-1">Jawaban Siswa</span>
                                            @endif
                                            @if($correctAnswer === 'true')
                                                <span class="badge bg-success ms-1">Kunci</span>
                                            @endif
                                        </div>
                                        <div class="p-2 px-4 rounded {{ $studentAnswer === 'false' ? ($studentAnswer === $correctAnswer ? 'bg-light-success' : 'bg-light-danger') : ($correctAnswer === 'false' ? 'bg-light-success' : 'bg-light') }}">
                                            Salah
                                            @if($studentAnswer === 'false')
                                                <span class="badge bg-primary ms-1">Jawaban Siswa</span>
                                            @endif
                                            @if($correctAnswer === 'false')
                                                <span class="badge bg-success ms-1">Kunci</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Score Input -->
                            @if($answer)
                                <div class="border-top pt-3 mt-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <label class="form-label">Nilai untuk soal ini:</label>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <input type="number" name="scores[{{ $answer->id }}]" 
                                                       value="{{ old('scores.' . $answer->id, $answer->points_earned ?? ($answer->is_correct ? $question->points : 0)) }}"
                                                       min="0" max="{{ $question->points ?? 1 }}" step="0.5"
                                                       class="form-control @error('scores.' . $answer->id) is-invalid @enderror">
                                                <span class="input-group-text">/ {{ $question->points ?? 1 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-lg-4">
                <!-- Summary & Submit -->
                <div class="card position-sticky" style="top: 80px;">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="ph-duotone ph-calculator text-primary me-2"></i>Ringkasan Nilai</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Soal:</span>
                                <span class="fw-bold">{{ $exam->questions->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Dijawab:</span>
                                <span class="fw-bold">{{ $attempt->answers->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Poin Maksimal:</span>
                                <span class="fw-bold">{{ $exam->questions->sum('points') }}</span>
                            </div>
                            @if($attempt->score !== null)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Nilai Saat Ini:</span>
                                    <span class="fw-bold text-primary">{{ number_format($attempt->score, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label for="feedback" class="form-label">Feedback untuk Siswa (opsional):</label>
                            <textarea name="feedback" id="feedback" rows="4" class="form-control"
                                      placeholder="Berikan komentar atau masukan...">{{ old('feedback', $attempt->feedback) }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="ph-duotone ph-check-circle me-2"></i>Simpan Nilai
                            </button>
                            <a href="{{ route('teacher.exams.results', $exam) }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('styles')
<style>
    .question-text img {
        max-width: 100%;
        height: auto;
    }
</style>
@endpush
