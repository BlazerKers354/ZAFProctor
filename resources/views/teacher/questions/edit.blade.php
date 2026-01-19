@extends('layouts.teacher')

@section('title', 'Edit Soal')
@section('page-title', 'Edit Soal')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="page-header-title">
                                <h5 class="m-b-10">Edit Soal</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Ujian</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.questions.index', $exam) }}">{{ Str::limit($exam->title, 15) }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit</li>
                            </ul>
                        </div>
                        <a href="{{ route('teacher.questions.index', $exam) }}" class="btn btn-outline-secondary">
                            <i class="ph ph-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('teacher.questions.update', [$exam, $question]) }}" method="POST" 
          x-data="questionForm()">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Question Type (Read-only) -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-list-checks text-primary me-2"></i>Tipe Soal
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($question->type === 'multiple_choice')
                            <div class="alert alert-info mb-0">
                                <i class="ph ph-check-square me-2"></i>
                                <strong>Pilihan Ganda</strong>
                                <small class="d-block mt-1 text-muted">Tipe soal tidak dapat diubah setelah dibuat</small>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="ph ph-text-aa me-2"></i>
                                <strong>Essay</strong>
                                <small class="d-block mt-1 text-muted">Tipe soal tidak dapat diubah setelah dibuat</small>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Question Text -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-question text-info me-2"></i>Pertanyaan
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea name="question" id="question" rows="4"
                                  class="form-control @error('question') is-invalid @enderror"
                                  placeholder="Tulis pertanyaan di sini..."
                                  required>{{ old('question', $question->question) }}</textarea>
                        @error('question')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Multiple Choice Options -->
                @if($question->type === 'multiple_choice')
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ph-duotone ph-list-bullets text-warning me-2"></i>Pilihan Jawaban
                            </h5>
                        </div>
                        <div class="card-body">
                            <template x-for="(option, index) in options" :key="index">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="form-check">
                                        <input type="radio" name="correct_option" :value="index"
                                               x-model="correctOption"
                                               class="form-check-input" required>
                                    </div>
                                    <span class="fw-medium text-muted" x-text="String.fromCharCode(65 + index) + '.'"></span>
                                    <div class="flex-grow-1">
                                        <input type="text" :name="'options[' + index + '][text]'" x-model="option.text"
                                               class="form-control"
                                               placeholder="Tulis pilihan jawaban..."
                                               required>
                                    </div>
                                    <button type="button" @click="removeOption(index)" 
                                            x-show="options.length > 2"
                                            class="btn btn-sm btn-light-danger">
                                        <i class="ph ph-x"></i>
                                    </button>
                                </div>
                            </template>
                            
                            <div class="d-flex align-items-center justify-content-between mt-3">
                                <button type="button" @click="addOption()" 
                                        x-show="options.length < 8"
                                        class="btn btn-sm btn-outline-primary">
                                    <i class="ph ph-plus me-1"></i>Tambah Pilihan
                                </button>
                                <small class="text-muted">
                                    <i class="ph ph-info me-1"></i>Pilih radio button untuk menandai jawaban yang benar
                                </small>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Explanation -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-lightbulb text-warning me-2"></i>Penjelasan (Opsional)
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea name="explanation" id="explanation" rows="3"
                                  class="form-control"
                                  placeholder="Tulis penjelasan jawaban yang benar...">{{ old('explanation', $question->explanation) }}</textarea>
                        <small class="text-muted mt-2 d-block">
                            <i class="ph ph-info me-1"></i>Penjelasan ini akan ditampilkan ke peserta setelah ujian selesai (jika diizinkan)
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Points -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-star text-warning me-2"></i>Poin
                        </h5>
                    </div>
                    <div class="card-body">
                        <input type="number" name="points" id="points" 
                               value="{{ old('points', $question->points) }}" min="1" max="100"
                               class="form-control text-center fs-4 fw-bold"
                               required>
                        <small class="text-muted mt-2 d-block text-center">Nilai untuk soal ini</small>
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="ph-duotone ph-floppy-disk me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('teacher.questions.index', $exam) }}"
                               class="btn btn-outline-danger">
                                <i class="ph ph-x me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Question Info -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="ph ph-info me-2"></i>Informasi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Dibuat:</small><br>
                            <small>{{ $question->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <div>
                            <small class="text-muted">Terakhir diubah:</small><br>
                            <small>{{ $question->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    function questionForm() {
        return {
            questionType: '{{ $question->type }}',
            correctOption: {{ $question->options->search(function($opt) { return $opt->is_correct; }) ?: 0 }},
            options: [
                @foreach($question->options->sortBy('order') as $option)
                { text: '{{ addslashes($option->option_text) }}' },
                @endforeach
            ],
            
            addOption() {
                if (this.options.length < 8) {
                    this.options.push({ text: '' });
                }
            },
            
            removeOption(index) {
                if (this.options.length > 2) {
                    this.options.splice(index, 1);
                    if (this.correctOption >= this.options.length) {
                        this.correctOption = this.options.length - 1;
                    }
                }
            }
        }
    }
</script>
@endpush
