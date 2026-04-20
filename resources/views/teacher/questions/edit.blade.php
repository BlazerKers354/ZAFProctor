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
                                <h5 class="m-b-10">Edit Soal #{{ $question->order }}</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph ph-house"></i></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Ujian</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.questions.index', $exam) }}">{{ Str::limit($exam->title, 15) }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Soal</li>
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

    <!-- Error Messages -->
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

    <form action="{{ route('teacher.questions.update', [$exam, $question]) }}" method="POST" enctype="multipart/form-data"
          x-data="questionForm()" @submit="validateForm($event)">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Question Type (Read-only) -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-list-checks text-primary me-2"></i>Tipe Soal
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($question->type === 'multiple_choice')
                            <div class="d-flex align-items-center p-3 bg-light-success rounded">
                                <i class="ph ph-check-square text-success me-3" style="font-size: 32px;"></i>
                                <div>
                                    <h6 class="mb-0">Pilihan Ganda</h6>
                                    <small class="text-muted">Jawaban otomatis dinilai oleh sistem</small>
                                </div>
                            </div>
                        @else
                            <div class="d-flex align-items-center p-3 bg-light-info rounded">
                                <i class="ph ph-text-aa text-info me-3" style="font-size: 32px;"></i>
                                <div>
                                    <h6 class="mb-0">Essay</h6>
                                    <small class="text-muted">Perlu dinilai manual oleh guru</small>
                                </div>
                            </div>
                        @endif
                        <small class="text-muted mt-2 d-block">
                            <i class="ph ph-info me-1"></i>Tipe soal tidak dapat diubah setelah dibuat
                        </small>
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
                                  required>{{ old('question', $question->question) }}</textarea>
                        @error('question')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                        <!-- Existing Image -->
                        @if($question->question_image_url)
                            <div class="mb-3 p-3 bg-light rounded" x-show="!removeExistingImage">
                                <div class="text-center">
                                    <img src="{{ $question->question_image_url }}" class="img-fluid rounded mb-2" style="max-height: 200px;">
                                    <div>
                                        <button type="button" @click="removeExistingImage = true" class="btn btn-sm btn-outline-danger">
                                            <i class="ph ph-trash me-1"></i>Hapus Gambar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="remove_image" :value="removeExistingImage ? 1 : 0">
                        @endif

                        <!-- Upload New Image -->
                        <div class="border border-2 border-dashed rounded p-4 text-center"
                             x-show="removeExistingImage || !hasExistingImage"
                             @dragover.prevent="dragover = true" 
                             @dragleave.prevent="dragover = false"
                             @drop.prevent="handleDrop($event)"
                             :class="{'border-primary bg-light-primary': dragover}">
                            <div x-show="!imagePreview">
                                <div>
                                    <i class="ph ph-image mb-2" style="font-size: 48px; color: #ccc;"></i>
                                    <div class="mb-2">
                                        <label for="question_image" class="btn btn-sm btn-outline-primary">
                                            <i class="ph ph-upload-simple me-1"></i>Pilih Gambar Baru
                                        </label>
                                        <input id="question_image" name="question_image" type="file" class="d-none" 
                                               accept="image/jpeg,image/png,image/jpg,image/gif" @change="previewImage($event)">
                                    </div>
                                    <small class="text-muted">PNG, JPG, GIF maksimal 2MB</small>
                                </div>
                            </div>
                            <div x-show="imagePreview">
                                <div>
                                    <img :src="imagePreview" class="img-fluid rounded mb-2" style="max-height: 200px;">
                                    <div>
                                        <button type="button" @click="removeNewImage()" class="btn btn-sm btn-outline-danger">
                                            <i class="ph ph-x me-1"></i>Batalkan
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
                @if($question->type === 'multiple_choice')
                    <div class="card mb-4">
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
                                Klik radio button di samping pilihan untuk menandai <strong>jawaban yang benar</strong>.
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
                                                   required>
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

                            <!-- Validation error -->
                            <div x-show="showOptionError" class="alert alert-danger mt-3">
                                <i class="ph ph-warning me-2"></i>
                                <span x-text="optionErrorMessage"></span>
                            </div>
                        </div>
                    </div>
                @endif

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
                                  placeholder="Tulis penjelasan mengapa jawaban tersebut benar...">{{ old('explanation', $question->explanation) }}</textarea>
                        <small class="text-muted mt-2 d-block">
                            <i class="ph ph-info me-1"></i>Penjelasan akan ditampilkan ke peserta setelah ujian selesai (jika diizinkan)
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
                               value="{{ old('points', $question->points) }}" min="1" max="100"
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

                <!-- Submit Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="ph ph-floppy-disk me-2"></i>Simpan Perubahan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="ph ph-floppy-disk me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('teacher.questions.index', $exam) }}"
                               class="btn btn-outline-secondary">
                                <i class="ph ph-x me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Question Info -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="ph ph-info me-2"></i>Informasi Soal
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="40%">Nomor Urut:</td>
                                <td><span class="badge bg-primary">{{ $question->order }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tipe:</td>
                                <td>
                                    @if($question->type === 'multiple_choice')
                                        <span class="badge bg-success">Pilihan Ganda</span>
                                    @else
                                        <span class="badge bg-info">Essay</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Dibuat:</td>
                                <td>{{ $question->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Diubah:</td>
                                <td>{{ $question->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
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
@php
    $initialOptions = $question->options
        ->sortBy('order')
        ->values()
        ->map(function ($option) {
            return [
                'text' => $option->option_text,
                'is_correct' => (bool) $option->is_correct,
            ];
        })
        ->all();
@endphp
<script id="question-edit-initial-data" type="application/json">
{!! json_encode([
    'questionType' => $question->type,
    'options' => $initialOptions,
    'hasExistingImage' => (bool) $question->question_image_url,
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>
<script>
    function questionForm() {
        const initialDataEl = document.getElementById('question-edit-initial-data');
        let initialData = {};
        if (initialDataEl) {
            try {
                initialData = JSON.parse(initialDataEl.textContent || '{}');
            } catch (error) {
                initialData = {};
            }
        }

        const rawOptions = Array.isArray(initialData.options) ? initialData.options : [];
        let correctIndex = rawOptions.findIndex((option) => Boolean(option && option.is_correct));
        if (correctIndex < 0) {
            correctIndex = 0;
        }

        const options = rawOptions.map((option) => ({
            text: option && typeof option.text === 'string' ? option.text : ''
        }));

        while (options.length < 2) {
            options.push({ text: '' });
        }

        return {
            questionType: typeof initialData.questionType === 'string'
                ? initialData.questionType
                : 'multiple_choice',
            correctOption: correctIndex,
            options,
            hasExistingImage: Boolean(initialData.hasExistingImage),
            removeExistingImage: false,
            imagePreview: null,
            dragover: false,
            showOptionError: false,
            optionErrorMessage: '',
            
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
                    } else if (this.correctOption > index) {
                        this.correctOption--;
                    }
                }
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

            removeNewImage() {
                this.imagePreview = null;
                document.getElementById('question_image').value = '';
                if (this.hasExistingImage) {
                    this.removeExistingImage = false;
                }
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
                    const validOptions = this.options.filter(o => o.text.trim().length > 0);
                    
                    if (validOptions.length < 2) {
                        event.preventDefault();
                        this.showOptionError = true;
                        this.optionErrorMessage = 'Minimal 2 pilihan jawaban harus diisi.';
                        return false;
                    }

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
