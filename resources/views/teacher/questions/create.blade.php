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
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph ph-house"></i></a></li>
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

    <!-- Validation Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading mb-2"><i class="ph ph-warning me-2"></i>Terdapat kesalahan:</h6>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Exam Info Card -->
    <div class="card mb-4 border-primary">
        <div class="card-body py-3">
            <div class="d-flex align-items-center">
                <div class="bg-light-primary rounded p-3 me-3">
                    <i class="ph ph-file-text text-primary" style="font-size: 24px;"></i>
                </div>
                <div>
                    <h6 class="mb-1">{{ $exam->title }}</h6>
                    <small class="text-muted">
                        {{ $exam->questions()->count() }} soal | 
                        Total {{ $exam->questions()->sum('points') }} poin
                    </small>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('teacher.questions.store', $exam) }}" method="POST" enctype="multipart/form-data" 
          x-data="questionForm()" @submit="validateForm($event)">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Question Type -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-list-checks text-primary me-2"></i>Tipe Soal
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check card p-3 h-100" 
                                     :class="{'border-primary bg-light-primary': questionType === 'multiple_choice'}">
                                    <input type="radio" name="question_type" value="multiple_choice" 
                                           x-model="questionType" id="type_mc"
                                           class="form-check-input" style="position: absolute; top: 15px; right: 15px;">
                                    <label for="type_mc" class="form-check-label cursor-pointer w-100">
                                        <div class="text-center">
                                            <i class="ph ph-check-square mb-2" style="font-size: 32px;"></i>
                                            <h6 class="mb-1">Pilihan Ganda</h6>
                                            <small class="text-muted">Jawaban otomatis dinilai</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check card p-3 h-100"
                                     :class="{'border-primary bg-light-primary': questionType === 'essay'}">
                                    <input type="radio" name="question_type" value="essay" 
                                           x-model="questionType" id="type_essay"
                                           class="form-check-input" style="position: absolute; top: 15px; right: 15px;">
                                    <label for="type_essay" class="form-check-label cursor-pointer w-100">
                                        <div class="text-center">
                                            <i class="ph ph-text-aa mb-2" style="font-size: 32px;"></i>
                                            <h6 class="mb-1">Essay</h6>
                                            <small class="text-muted">Perlu dinilai manual</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Question Text -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-question text-info me-2"></i>Pertanyaan <span class="text-danger">*</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea name="question" id="question" rows="4" x-ref="questionText"
                                  class="form-control @error('question') is-invalid @enderror"
                                  placeholder="Tulis pertanyaan di sini... (minimal 10 karakter)"
                                  required>{{ old('question') }}</textarea>
                        @error('question')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted mt-2 d-block">
                            <i class="ph ph-lightbulb me-1"></i>Tips: Tulis pertanyaan dengan jelas dan hindari pertanyaan ambigu
                        </small>
                    </div>
                </div>
                
                <!-- Question Image -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-image text-success me-2"></i>Gambar (Opsional)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="border border-2 border-dashed rounded p-4 text-center" id="imageDropzone"
                             @dragover.prevent="dragover = true" 
                             @dragleave.prevent="dragover = false"
                             @drop.prevent="handleDrop($event)"
                             :class="{'border-primary bg-light-primary': dragover}">
                            <div x-show="!imagePreview">
                                <div>
                                    <i class="ph ph-image mb-2" style="font-size: 48px; color: #ccc;"></i>
                                    <div class="mb-2">
                                        <label for="question_image" class="btn btn-sm btn-outline-primary">
                                            <i class="ph ph-upload-simple me-1"></i>Pilih Gambar
                                        </label>
                                        <input id="question_image" name="question_image" type="file" class="d-none" 
                                               accept="image/jpeg,image/png,image/jpg,image/gif" @change="previewImage($event)">
                                    </div>
                                    <small class="text-muted">PNG, JPG, GIF maksimal 2MB<br>Drag & drop atau klik untuk upload</small>
                                </div>
                            </div>
                            <div x-show="imagePreview">
                                <div>
                                    <img :src="imagePreview" class="img-fluid rounded mb-2" style="max-height: 200px;">
                                    <div>
                                        <button type="button" @click="removeImage()" class="btn btn-sm btn-outline-danger">
                                            <i class="ph ph-trash me-1"></i>Hapus Gambar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('question_image')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Multiple Choice Options -->
                <div class="card mb-4" x-show="questionType === 'multiple_choice'" x-cloak>
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="ph ph-list-dashes text-warning me-2"></i>Pilihan Jawaban <span class="text-danger">*</span>
                            </h5>
                            <span class="badge bg-light-info">
                                <span x-text="options.filter(o => o.text.trim()).length"></span> pilihan aktif
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <i class="ph ph-info me-2"></i>
                            <strong>Petunjuk:</strong> Klik radio button di samping pilihan untuk menandai <strong>jawaban yang benar</strong>.
                            Minimal 2 pilihan jawaban harus diisi.
                        </div>

                        <template x-for="(option, index) in options" :key="index">
                            <div class="option-item mb-3 p-3 rounded" 
                                 :class="{
                                     'bg-light-success border border-success': correctOption === index && option.text.trim(),
                                     'bg-light': correctOption !== index
                                 }">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="form-check pt-2">
                                        <input type="radio" name="correct_option" :value="index"
                                               x-model.number="correctOption"
                                               class="form-check-input"
                                               :id="'correct_' + index"
                                               style="width: 20px; height: 20px;">
                                    </div>
                                    <div class="option-label pt-2">
                                        <span class="badge fs-6" 
                                              :class="correctOption === index && option.text.trim() ? 'bg-success' : 'bg-secondary'"
                                              x-text="String.fromCharCode(65 + index)"></span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="text" :name="'options[' + index + '][text]'" x-model="option.text"
                                               class="form-control"
                                               :class="{'border-success': correctOption === index && option.text.trim()}"
                                               :placeholder="'Pilihan jawaban ' + String.fromCharCode(65 + index) + '...'"
                                               @input="validateOption(index)">
                                        <small x-show="correctOption === index && option.text.trim()" class="text-success">
                                            <i class="ph ph-check-circle me-1"></i>Jawaban Benar
                                        </small>
                                    </div>
                                    <button type="button" @click="removeOption(index)" 
                                            x-show="options.length > 2"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Hapus pilihan">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                        
                        <div class="d-flex align-items-center justify-content-between mt-3 pt-3 border-top">
                            <button type="button" @click="addOption()" 
                                    x-show="options.length < 8"
                                    class="btn btn-outline-primary">
                                <i class="ph ph-plus me-1"></i>Tambah Pilihan
                                <span class="badge bg-primary ms-1" x-text="String.fromCharCode(65 + options.length)"></span>
                            </button>
                            <small class="text-muted" x-show="options.length >= 8">
                                <i class="ph ph-warning me-1"></i>Maksimal 8 pilihan
                            </small>
                        </div>

                        <!-- Hidden validation indicator -->
                        <div x-show="showOptionError" class="alert alert-danger mt-3">
                            <i class="ph ph-warning me-2"></i>
                            <span x-text="optionErrorMessage"></span>
                        </div>
                    </div>
                </div>

                <!-- Essay Guidelines (for essay type) -->
                <div class="card mb-4" x-show="questionType === 'essay'" x-cloak>
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-note text-info me-2"></i>Panduan Jawaban (Opsional)
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea name="answer_guidelines" id="answer_guidelines" rows="4"
                                  class="form-control"
                                  placeholder="Tulis kata kunci atau poin-poin yang harus ada dalam jawaban...">{{ old('answer_guidelines') }}</textarea>
                        <small class="text-muted mt-2 d-block">
                            <i class="ph ph-info me-1"></i>Panduan ini akan membantu saat menilai jawaban essay siswa
                        </small>
                    </div>
                </div>

                <!-- Explanation -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-lightbulb text-warning me-2"></i>Penjelasan / Pembahasan (Opsional)
                        </h5>
                    </div>
                    <div class="card-body">
                        <textarea name="explanation" id="explanation" rows="3"
                                  class="form-control"
                                  placeholder="Tulis penjelasan mengapa jawaban tersebut benar...">{{ old('explanation') }}</textarea>
                        <small class="text-muted mt-2 d-block">
                            <i class="ph ph-info me-1"></i>Penjelasan akan ditampilkan ke peserta setelah ujian selesai (jika diizinkan di pengaturan)
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Points -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-star text-warning me-2"></i>Poin Soal <span class="text-danger">*</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <input type="number" name="points" id="points" 
                               value="{{ old('points', 10) }}" min="1" max="100"
                               class="form-control text-center fs-3 fw-bold @error('points') is-invalid @enderror"
                               required>
                        @error('points')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="mt-3">
                            <div class="btn-group w-100">
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('points').value = 5">5</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('points').value = 10">10</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('points').value = 15">15</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('points').value = 20">20</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('points').value = 25">25</button>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block text-center">Poin: 1-100</small>
                    </div>
                </div>

                <!-- Question Preview -->
                <div class="card mb-4" x-show="questionType === 'multiple_choice'">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="ph ph-eye me-2"></i>Preview Soal
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="preview-box p-3 bg-light rounded">
                            <p class="mb-3 text-muted" x-text="$refs.questionText?.value || 'Pertanyaan akan muncul di sini...'"></p>
                            <template x-for="(option, index) in options.filter(o => o.text.trim())" :key="'preview-' + index">
                                <div class="form-check mb-2">
                                    <input type="radio" class="form-check-input" disabled>
                                    <label class="form-check-label small">
                                        <span x-text="String.fromCharCode(65 + index) + '. ' + option.text"></span>
                                    </label>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="ph ph-floppy-disk me-2"></i>Simpan Soal
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="save_and_new"
                                    class="btn btn-primary btn-lg">
                                <i class="ph ph-circle-plus me-2"></i>Simpan & Tambah Lagi
                            </button>
                            <button type="submit" name="action" value="save"
                                    class="btn btn-outline-success">
                                <i class="ph ph-check-circle me-2"></i>Simpan & Selesai
                            </button>
                            <a href="{{ route('teacher.questions.index', $exam) }}"
                               class="btn btn-outline-secondary">
                                <i class="ph ph-x me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="card mt-4 border-info">
                    <div class="card-header bg-light-info">
                        <h6 class="mb-0 text-info">
                            <i class="ph ph-lightbulb me-2"></i>Tips Membuat Soal
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0 ps-3">
                            <li class="mb-2">Gunakan bahasa yang jelas dan mudah dipahami</li>
                            <li class="mb-2">Hindari pertanyaan negatif ganda</li>
                            <li class="mb-2">Untuk pilihan ganda, buat semua pilihan tampak masuk akal</li>
                            <li class="mb-2">Berikan poin sesuai tingkat kesulitan soal</li>
                            <li>Tambahkan penjelasan untuk membantu siswa belajar</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .cursor-pointer { cursor: pointer; }
    .option-item {
        transition: all 0.2s ease;
    }
    .option-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script id="question-create-initial-data" type="application/json">
{!! json_encode([
    'questionType' => old('question_type', 'multiple_choice'),
    'correctOption' => (int) old('correct_option', 0),
    'options' => [
        ['text' => old('options.0.text', '')],
        ['text' => old('options.1.text', '')],
        ['text' => old('options.2.text', '')],
        ['text' => old('options.3.text', '')],
    ],
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>
<script>
    function questionForm() {
        const initialDataEl = document.getElementById('question-create-initial-data');
        let initialData = {};
        if (initialDataEl) {
            try {
                initialData = JSON.parse(initialDataEl.textContent || '{}');
            } catch (error) {
                initialData = {};
            }
        }

        const parsedCorrectOption = Number(initialData.correctOption);
        const normalizedCorrectOption = Number.isInteger(parsedCorrectOption) ? parsedCorrectOption : 0;
        const normalizedOptions = Array.isArray(initialData.options) && initialData.options.length > 0
            ? initialData.options.map((option) => ({
                text: option && typeof option.text === 'string' ? option.text : ''
            }))
            : [
                { text: '' },
                { text: '' },
                { text: '' },
                { text: '' }
            ];

        return {
            questionType: typeof initialData.questionType === 'string'
                ? initialData.questionType
                : 'multiple_choice',
            correctOption: normalizedCorrectOption,
            options: normalizedOptions,
            imagePreview: null,
            dragover: false,
            showOptionError: false,
            optionErrorMessage: '',
            pointsManuallySet: false,
            defaultPoints: { multiple_choice: 2, essay: 10 },

            init() {
                const pointsEl = document.getElementById('points');
                if (pointsEl) {
                    pointsEl.addEventListener('input', () => { this.pointsManuallySet = true; });
                }
                this.$watch('questionType', (val) => {
                    if (!this.pointsManuallySet && pointsEl) {
                        pointsEl.value = this.defaultPoints[val] || 10;
                    }
                });
            },
            
            addOption() {
                if (this.options.length < 8) {
                    this.options.push({ text: '' });
                }
            },
            
            removeOption(index) {
                if (this.options.length > 2) {
                    this.options.splice(index, 1);
                    // Adjust correct option if needed
                    if (this.correctOption >= this.options.length) {
                        this.correctOption = this.options.length - 1;
                    } else if (this.correctOption > index) {
                        this.correctOption--;
                    }
                }
            },

            validateOption(index) {
                this.showOptionError = false;
            },

            previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar. Maksimal 2MB.');
                        event.target.value = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            removeImage() {
                this.imagePreview = null;
                document.getElementById('question_image').value = '';
            },

            handleDrop(event) {
                this.dragover = false;
                const file = event.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    const input = document.getElementById('question_image');
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    input.files = dataTransfer.files;
                    this.previewImage({ target: input });
                }
            },

            validateForm(event) {
                if (this.questionType === 'multiple_choice') {
                    // Count valid options
                    const validOptions = this.options.filter(o => o.text.trim().length > 0);
                    
                    if (validOptions.length < 2) {
                        event.preventDefault();
                        this.showOptionError = true;
                        this.optionErrorMessage = 'Minimal 2 pilihan jawaban harus diisi.';
                        return false;
                    }

                    // Check if correct option has text
                    if (!this.options[this.correctOption] || !this.options[this.correctOption].text.trim()) {
                        event.preventDefault();
                        this.showOptionError = true;
                        this.optionErrorMessage = 'Jawaban yang dipilih sebagai benar tidak boleh kosong.';
                        return false;
                    }
                }
                return true;
            }
        }
    }
</script>
@endpush
