@extends('layouts.teacher')

@section('title', 'Tambah Soal')
@section('page-title', 'Tambah Soal')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="page-header-title">
                                <h5 class="m-b-10">Tambah Soal Baru</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Ujian</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.questions.index', $exam) }}">{{ Str::limit($exam->title, 15) }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Tambah Soal</li>
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

    <form action="{{ route('teacher.questions.store', $exam) }}" method="POST" enctype="multipart/form-data" 
          x-data="questionForm()">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Question Type -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-list-checks text-primary me-2"></i>Tipe Soal
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input type="radio" name="question_type" value="multiple_choice" 
                                       x-model="questionType" id="type_mc"
                                       class="form-check-input" checked>
                                <label for="type_mc" class="form-check-label">
                                    <i class="ph ph-check-square me-1"></i>Pilihan Ganda
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="question_type" value="essay" 
                                       x-model="questionType" id="type_essay"
                                       class="form-check-input">
                                <label for="type_essay" class="form-check-label">
                                    <i class="ph ph-text-aa me-1"></i>Essay
                                </label>
                            </div>
                        </div>
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
                                  required>{{ old('question') }}</textarea>
                        @error('question')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Question Image -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-image text-success me-2"></i>Gambar (Opsional)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="border border-2 border-dashed rounded p-4 text-center">
                            <i class="ph-duotone ph-image mb-2" style="font-size: 48px; color: #ccc;"></i>
                            <div class="mb-2">
                                <label for="question_image" class="btn btn-sm btn-outline-primary">
                                    <i class="ph ph-upload me-1"></i>Upload Gambar
                                </label>
                                <input id="question_image" name="question_image" type="file" class="d-none" accept="image/*">
                            </div>
                            <small class="text-muted">PNG, JPG, GIF maksimal 2MB</small>
                        </div>
                    </div>
                </div>

                <!-- Multiple Choice Options -->
                <div class="card mb-4" x-show="questionType === 'multiple_choice'">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-list-bullets text-warning me-2"></i>Pilihan Jawaban
                        </h5>
                    </div>
                    <div class="card-body">
                        <template x-for="(option, index) in options" :key="index">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="form-check">
                                    <input type="radio" :name="'correct_option'" :value="index"
                                           x-model="correctOption"
                                           class="form-check-input">
                                </div>
                                <span class="fw-medium text-muted" x-text="String.fromCharCode(65 + index) + '.'"></span>
                                <div class="flex-grow-1">
                                    <input type="text" :name="'options[' + index + '][text]'" x-model="option.text"
                                           class="form-control"
                                           placeholder="Tulis pilihan jawaban...">
                                    <input type="hidden" :name="'options[' + index + '][label]'" :value="String.fromCharCode(65 + index)">
                                </div>
                                <button type="button" @click="removeOption(index)" 
                                        x-show="options.length > 2"
                                        class="btn btn-sm btn-light-danger">
                                    <i class="ph ph-x"></i>
                                </button>
                            </div>
                        </template>
                        
                        <input type="hidden" name="correct_option_index" :value="correctOption">
                        
                        <div class="d-flex align-items-center justify-content-between mt-3">
                            <button type="button" @click="addOption()" 
                                    x-show="options.length < 6"
                                    class="btn btn-sm btn-outline-primary">
                                <i class="ph ph-plus me-1"></i>Tambah Pilihan
                            </button>
                            <small class="text-muted">
                                <i class="ph ph-info me-1"></i>Pilih radio button untuk menandai jawaban yang benar
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Essay Answer Key -->
                <div class="card mb-4" x-show="questionType === 'essay'">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-key text-info me-2"></i>Kunci Jawaban (Opsional)
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea name="answer_key" id="answer_key" rows="4"
                                  class="form-control"
                                  placeholder="Tulis kunci jawaban atau pedoman penilaian...">{{ old('answer_key') }}</textarea>
                        <small class="text-muted mt-2 d-block">
                            <i class="ph ph-info me-1"></i>Kunci jawaban ini akan digunakan sebagai referensi saat menilai jawaban essay
                        </small>
                    </div>
                </div>

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
                                  placeholder="Tulis penjelasan jawaban yang benar...">{{ old('explanation') }}</textarea>
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
                               value="{{ old('points', 10) }}" min="1" max="100"
                               class="form-control text-center fs-4 fw-bold"
                               required>
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="save_and_new"
                                    class="btn btn-primary btn-lg">
                                <i class="ph-duotone ph-floppy-disk me-2"></i>Simpan & Tambah Lagi
                            </button>
                            <button type="submit" name="action" value="save"
                                    class="btn btn-outline-secondary">
                                <i class="ph-duotone ph-check me-2"></i>Simpan
                            </button>
                            <a href="{{ route('teacher.questions.index', $exam) }}"
                               class="btn btn-outline-danger">
                                <i class="ph ph-x me-2"></i>Batal
                            </a>
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
            questionType: '{{ old('question_type', 'multiple_choice') }}',
            correctOption: {{ old('correct_option_index', 0) }},
            options: [
                { text: '{{ old('options.0.text', '') }}' },
                { text: '{{ old('options.1.text', '') }}' },
                { text: '{{ old('options.2.text', '') }}' },
                { text: '{{ old('options.3.text', '') }}' }
            ],
            
            addOption() {
                if (this.options.length < 6) {
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
