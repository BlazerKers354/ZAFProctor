@extends('layouts.teacher')

@section('title', 'Edit Ujian')
@section('page-title', 'Edit Ujian')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="page-header-title">
                                <h5 class="m-b-10">Edit Ujian</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph ph-house"></i></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Ujian</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.show', $exam) }}">{{ Str::limit($exam->title, 20) }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit</li>
                            </ul>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-outline-secondary">
                                <i class="ph ph-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('teacher.exams.update', $exam) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-info text-primary me-2"></i>Informasi Dasar
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="title" class="form-label">Judul Ujian <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" value="{{ old('title', $exam->title) }}"
                                       class="form-control @error('title') is-invalid @enderror"
                                       placeholder="Contoh: Ujian Tengah Semester Matematika"
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="course_id" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                                <select name="course_id" id="course_id"
                                        class="form-select @error('course_id') is-invalid @enderror"
                                        required>
                                    <option value="">Pilih Mata Pelajaran</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id', $exam->course_id) == $course->id ? 'selected' : '' }}>
                                            {{ $course->code }} - {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Deskripsi Ujian</label>
                                <textarea name="description" id="description" rows="4"
                                          class="form-control"
                                          placeholder="Berikan deskripsi untuk ujian ini...">{{ old('description', $exam->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-calendar-dots text-info me-2"></i>Jadwal Ujian
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Tipe Ujian</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input type="radio" name="type" value="scheduled" id="type_scheduled"
                                           {{ old('type', $exam->type) === 'scheduled' ? 'checked' : '' }}
                                           class="form-check-input"
                                           onchange="toggleSchedule(true)">
                                    <label for="type_scheduled" class="form-check-label">
                                        <i class="ph ph-calendar-dots me-1"></i>Terjadwal
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="type" value="flexible" id="type_flexible"
                                           {{ old('type', $exam->type) === 'flexible' ? 'checked' : '' }}
                                           class="form-check-input"
                                           onchange="toggleSchedule(false)">
                                    <label for="type_flexible" class="form-check-label">
                                        <i class="ph ph-infinity me-1"></i>Fleksibel
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="schedule-fields" style="display: {{ old('type', $exam->type) === 'scheduled' ? 'block' : 'none' }};">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="start_time" class="form-label">Waktu Mulai</label>
                                    <input type="datetime-local" name="start_time" id="start_time"
                                           value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}"
                                           class="form-control @error('start_time') is-invalid @enderror"
                                           onchange="validateDuration()">
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="end_time" class="form-label">Waktu Selesai</label>
                                    <input type="datetime-local" name="end_time" id="end_time"
                                           value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}"
                                           class="form-control @error('end_time') is-invalid @enderror"
                                           onchange="validateDuration()">
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label for="duration" class="form-label">Durasi Pengerjaan (menit) <span class="text-danger">*</span></label>
                                <input type="number" name="duration" id="duration" value="{{ old('duration', $exam->duration) }}"
                                       min="5" max="480"
                                       class="form-control @error('duration') is-invalid @enderror"
                                       required
                                       onchange="validateDuration()">
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Waktu yang diberikan kepada siswa untuk mengerjakan ujian</small>
                            </div>
                            <div class="col-md-6">
                                <div id="duration-warning" class="alert alert-warning d-none mt-4">
                                    <i class="ph ph-warning me-1"></i>
                                    <span id="duration-warning-text"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Proctoring Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-shield-check text-success me-2"></i>Pengaturan Proctoring
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="ph ph-video-camera text-success me-2 fs-5"></i>
                                    <span class="fw-medium">Monitor Webcam</span>
                                    <span class="badge bg-success ms-2">Selalu Aktif</span>
                                </div>
                                <small class="text-muted">Webcam wajib aktif untuk semua ujian. Sistem akan mengambil snapshot berkala dan mendeteksi wajah peserta.</small>
                                <input type="hidden" name="webcam_enabled" value="1">
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="browser_lock_enabled" id="browser_lock_enabled" value="1"
                                           {{ old('browser_lock_enabled', $exam->settings?->browser_lock_enabled ?? true) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="browser_lock_enabled" class="form-check-label fw-medium">Browser Lock (Fullscreen)</label>
                                </div>
                                <small class="text-muted">Kunci browser dalam mode fullscreen selama ujian</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="tab_switch_detection" id="tab_switch_detection" value="1"
                                           {{ old('tab_switch_detection', $exam->settings?->tab_switch_detection ?? true) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="tab_switch_detection" class="form-check-label fw-medium">Deteksi Tab Switch</label>
                                </div>
                                <small class="text-muted">Deteksi perpindahan tab browser</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="block_keyboard_shortcuts" id="block_keyboard_shortcuts" value="1"
                                           {{ old('block_keyboard_shortcuts', $exam->settings?->block_keyboard_shortcuts ?? true) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="block_keyboard_shortcuts" class="form-check-label fw-medium">Blokir Kecurangan</label>
                                </div>
                                <small class="text-muted">Blokir copy/paste, klik kanan, dan keyboard shortcut terlarang</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="max_tab_switches" class="form-label">Maks Tab Switch / Pelanggaran</label>
                                <input type="number" name="max_tab_switches" id="max_tab_switches" 
                                       value="{{ old('max_tab_switches', $exam->settings?->max_tab_switches ?? 5) }}" 
                                       min="0" max="20"
                                       class="form-control">
                                <small class="text-muted">0 = unlimited. Ujian auto-submit jika melebihi batas</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Display Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-eye text-info me-2"></i>Pengaturan Tampilan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="shuffle_questions" id="shuffle_questions" value="1"
                                           {{ old('shuffle_questions', $exam->settings?->shuffle_questions) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="shuffle_questions" class="form-check-label fw-medium">Acak Soal</label>
                                </div>
                                <small class="text-muted">Urutan soal berbeda untuk setiap peserta</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="shuffle_options" id="shuffle_options" value="1"
                                           {{ old('shuffle_options', $exam->settings?->shuffle_options) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="shuffle_options" class="form-check-label fw-medium">Acak Opsi Jawaban</label>
                                </div>
                                <small class="text-muted">Urutan opsi berbeda untuk setiap peserta</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="show_correct_answers" id="show_correct_answers" value="1"
                                           {{ old('show_correct_answers', $exam->settings?->show_correct_answers) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="show_correct_answers" class="form-check-label fw-medium">Tampilkan Jawaban Benar</label>
                                </div>
                                <small class="text-muted">Tampilkan jawaban benar setelah selesai</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="show_score" id="show_score" value="1"
                                           {{ old('show_score', $exam->settings?->show_score ?? true) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="show_score" class="form-check-label fw-medium">Tampilkan Nilai</label>
                                </div>
                                <small class="text-muted">Peserta dapat melihat nilai langsung</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attempt Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-repeat text-warning me-2"></i>Pengaturan Percobaan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="max_attempts" class="form-label">Maksimal Percobaan</label>
                                <input type="number" name="max_attempts" id="max_attempts" 
                                       value="{{ old('max_attempts', $exam->settings?->max_attempts ?? 1) }}" 
                                       min="0" max="10"
                                       class="form-control">
                                <small class="text-muted">0 = unlimited</small>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="grade_method" class="form-label">Metode Penilaian</label>
                                <select name="grade_method" id="grade_method" class="form-select">
                                    <option value="highest" {{ old('grade_method', $exam->settings?->grade_method ?? 'highest') === 'highest' ? 'selected' : '' }}>Nilai Tertinggi</option>
                                    <option value="latest" {{ old('grade_method', $exam->settings?->grade_method) === 'latest' ? 'selected' : '' }}>Nilai Terakhir</option>
                                    <option value="average" {{ old('grade_method', $exam->settings?->grade_method) === 'average' ? 'selected' : '' }}>Rata-rata</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="passing_score" class="form-label">Nilai Minimum Lulus (%)</label>
                                <input type="number" name="passing_score" id="passing_score" 
                                       value="{{ old('passing_score', $exam->settings?->passing_score ?? 60) }}" min="0" max="100"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Access Token -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-key text-warning me-2"></i>Token Akses
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-2">
                            <input type="text" id="access_token" 
                                   value="{{ $exam->access_token }}"
                                   class="form-control font-monospace text-center fw-bold fs-5"
                                   readonly>
                            <button type="button" onclick="copyToClipboard('{{ $exam->access_token }}')" class="btn btn-outline-secondary" title="Salin">
                                <i class="ph ph-copy"></i>
                            </button>
                        </div>
                        <small class="text-muted">Token yang harus dimasukkan peserta untuk memulai ujian</small>
                        <div class="mt-2">
                            <form action="{{ route('teacher.exams.regenerate-token', $exam) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Token lama tidak akan bisa digunakan lagi. Lanjutkan?')">
                                    <i class="ph ph-arrows-clockwise me-1"></i>Regenerate Token
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-info text-primary me-2"></i>Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Status Ujian</label>
                            <select name="status" class="form-select">
                                <option value="draft" {{ old('status', $exam->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $exam->status) === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
                            </select>
                        </div>
                        
                        <div class="alert alert-info mb-0">
                            <small>
                                <strong>Draft:</strong> Hanya Anda yang dapat melihat<br>
                                <strong>Published:</strong> Peserta dapat melihat dan mengikuti ujian
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="ph ph-floppy-disk me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-outline-secondary">
                                <i class="ph ph-x me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="ph ph-clock-rotate-left me-2"></i>Informasi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Dibuat:</small><br>
                            <small>{{ $exam->created_at->format('d M Y, H:i') }}</small>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Terakhir diubah:</small><br>
                            <small>{{ $exam->updated_at->format('d M Y, H:i') }}</small>
                        </div>
                        <div>
                            <small class="text-muted">Total Soal:</small><br>
                            <small class="fw-semibold">{{ $exam->questions->count() }} soal</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    function toggleSchedule(show) {
        document.getElementById('schedule-fields').style.display = show ? 'block' : 'none';
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');

        if (!show) {
            startTimeInput.value = '';
            endTimeInput.value = '';
            // Hide duration warning for flexible exams
            document.getElementById('duration-warning').classList.add('d-none');
        } else {
            validateDuration();
        }
    }

    function validateDuration() {
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const duration = parseInt(document.getElementById('duration').value) || 0;
        const warningDiv = document.getElementById('duration-warning');
        const warningText = document.getElementById('duration-warning-text');
        const isScheduled = document.getElementById('type_scheduled').checked;

        if (!isScheduled || !startTime || !endTime || !duration) {
            warningDiv.classList.add('d-none');
            return true;
        }

        const start = new Date(startTime);
        const end = new Date(endTime);
        const timeWindowMinutes = (end - start) / (1000 * 60);

        if (timeWindowMinutes <= 0) {
            warningDiv.classList.remove('d-none');
            warningDiv.classList.remove('alert-warning');
            warningDiv.classList.add('alert-danger');
            warningText.textContent = 'Waktu selesai harus lebih besar dari waktu mulai.';
            return false;
        }

        if (duration > timeWindowMinutes) {
            warningDiv.classList.remove('d-none');
            warningDiv.classList.remove('alert-warning');
            warningDiv.classList.add('alert-danger');
            warningText.textContent = `Durasi ujian (${duration} menit) melebihi jendela waktu yang tersedia (${Math.floor(timeWindowMinutes)} menit). Durasi harus kurang dari atau sama dengan selisih waktu mulai dan selesai.`;
            return false;
        }

        warningDiv.classList.add('d-none');
        return true;
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Token berhasil disalin!');
        }).catch(function(err) {
            console.error('Could not copy text: ', err);
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const scheduledRadio = document.getElementById('type_scheduled');
        if (scheduledRadio) {
            toggleSchedule(scheduledRadio.checked);
        }

        // Form validation on submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const isScheduled = document.getElementById('type_scheduled').checked;
            if (isScheduled && !validateDuration()) {
                e.preventDefault();
                alert('Durasi ujian tidak boleh melebihi jendela waktu yang tersedia (selisih waktu mulai dan selesai).');
                return false;
            }
        });
    });
</script>
@endpush
