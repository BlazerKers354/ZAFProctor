@extends('layouts.teacher')

@section('title', 'Buat Ujian Baru')
@section('page-title', 'Buat Ujian')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="page-header-title">
                                <h5 class="m-b-10">Buat Ujian Baru</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Ujian</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Buat Baru</li>
                            </ul>
                        </div>
                        <a href="{{ route('teacher.exams.index') }}" class="btn btn-outline-secondary">
                            <i class="ph ph-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('teacher.exams.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-info text-primary me-2"></i>Informasi Dasar
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="title" class="form-label">Judul Ujian <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}"
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
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->code }} - {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Deskripsi/Petunjuk Ujian</label>
                                <textarea name="description" id="description" rows="4"
                                          class="form-control"
                                          placeholder="Berikan petunjuk untuk peserta ujian...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-calendar text-info me-2"></i>Jadwal Ujian
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Tipe Ujian</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input type="radio" name="type" value="scheduled" id="type_scheduled"
                                           {{ old('type', 'scheduled') === 'scheduled' ? 'checked' : '' }}
                                           class="form-check-input"
                                           onchange="toggleSchedule(true)">
                                    <label for="type_scheduled" class="form-check-label">
                                        <i class="ph ph-calendar me-1"></i>Terjadwal
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="type" value="flexible" id="type_flexible"
                                           {{ old('type') === 'flexible' ? 'checked' : '' }}
                                           class="form-check-input"
                                           onchange="toggleSchedule(false)">
                                    <label for="type_flexible" class="form-check-label">
                                        <i class="ph ph-infinity me-1"></i>Fleksibel
                                    </label>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <strong>Terjadwal:</strong> Ujian hanya bisa diakses pada waktu yang ditentukan<br>
                                <strong>Fleksibel:</strong> Ujian bisa diakses kapan saja setelah dipublikasikan
                            </small>
                        </div>

                        <div id="schedule-fields" style="display: {{ old('type', 'scheduled') === 'scheduled' ? 'block' : 'none' }};">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="start_time" class="form-label">Waktu Mulai</label>
                                    <input type="datetime-local" name="start_time" id="start_time"
                                           value="{{ old('start_time') }}"
                                           class="form-control @error('start_time') is-invalid @enderror"
                                           onchange="validateDuration()">
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="end_time" class="form-label">Waktu Selesai</label>
                                    <input type="datetime-local" name="end_time" id="end_time"
                                           value="{{ old('end_time') }}"
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
                                <input type="number" name="duration" id="duration" value="{{ old('duration', 60) }}"
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
                            <i class="ph-duotone ph-shield-check text-success me-2"></i>Pengaturan Proctoring
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="webcam_enabled" id="webcam_enabled" value="1"
                                           {{ old('webcam_enabled', true) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="webcam_enabled" class="form-check-label fw-medium">Monitor Webcam</label>
                                </div>
                                <small class="text-muted">Rekam aktivitas peserta via webcam</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="screen_capture_enabled" id="screen_capture_enabled" value="1"
                                           {{ old('screen_capture_enabled', true) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="screen_capture_enabled" class="form-check-label fw-medium">Screen Capture</label>
                                </div>
                                <small class="text-muted">Tangkap screenshot layar secara berkala</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="browser_lock_enabled" id="browser_lock_enabled" value="1"
                                           {{ old('browser_lock_enabled', true) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="browser_lock_enabled" class="form-check-label fw-medium">Browser Lock (Fullscreen)</label>
                                </div>
                                <small class="text-muted">Kunci browser dalam mode fullscreen selama ujian</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="tab_switch_detection" id="tab_switch_detection" value="1"
                                           {{ old('tab_switch_detection', true) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="tab_switch_detection" class="form-check-label fw-medium">Deteksi Tab Switch</label>
                                </div>
                                <small class="text-muted">Deteksi perpindahan tab browser</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="max_tab_switches" class="form-label">Maks Tab Switch / Pelanggaran</label>
                                <input type="number" name="max_tab_switches" id="max_tab_switches" 
                                       value="{{ old('max_tab_switches', 5) }}" 
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
                            <i class="ph-duotone ph-eye text-info me-2"></i>Pengaturan Tampilan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="shuffle_questions" id="shuffle_questions" value="1"
                                           {{ old('shuffle_questions') ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="shuffle_questions" class="form-check-label fw-medium">Acak Soal</label>
                                </div>
                                <small class="text-muted">Urutan soal berbeda untuk setiap peserta</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="shuffle_options" id="shuffle_options" value="1"
                                           {{ old('shuffle_options') ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="shuffle_options" class="form-check-label fw-medium">Acak Opsi Jawaban</label>
                                </div>
                                <small class="text-muted">Urutan opsi berbeda untuk setiap peserta</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="show_correct_answers" id="show_correct_answers" value="1"
                                           {{ old('show_correct_answers') ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="show_correct_answers" class="form-check-label fw-medium">Tampilkan Jawaban Benar</label>
                                </div>
                                <small class="text-muted">Tampilkan jawaban benar setelah selesai</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="show_score" id="show_score" value="1"
                                           {{ old('show_score', true) ? 'checked' : '' }}
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
                            <i class="ph-duotone ph-repeat text-warning me-2"></i>Pengaturan Percobaan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="max_attempts" class="form-label">Maksimal Percobaan</label>
                                <input type="number" name="max_attempts" id="max_attempts" 
                                       value="{{ old('max_attempts', 1) }}" 
                                       min="0" max="10"
                                       class="form-control">
                                <small class="text-muted">0 = unlimited</small>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="grade_method" class="form-label">Metode Penilaian</label>
                                <select name="grade_method" id="grade_method" class="form-select">
                                    <option value="highest" {{ old('grade_method', 'highest') === 'highest' ? 'selected' : '' }}>Nilai Tertinggi</option>
                                    <option value="latest" {{ old('grade_method') === 'latest' ? 'selected' : '' }}>Nilai Terakhir</option>
                                    <option value="average" {{ old('grade_method') === 'average' ? 'selected' : '' }}>Rata-rata</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="passing_score" class="form-label">Nilai Minimum Lulus (%)</label>
                                <input type="number" name="passing_score" id="passing_score" 
                                       value="{{ old('passing_score', 60) }}" min="0" max="100"
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
                            <i class="ph-duotone ph-key text-warning me-2"></i>Token Akses
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-2">
                            <input type="text" name="access_token" id="access_token" 
                                   value="{{ old('access_token', strtoupper(Str::random(8))) }}"
                                   class="form-control font-monospace text-center fw-bold fs-5"
                                   readonly>
                            <button type="button" onclick="generateToken()" class="btn btn-outline-secondary">
                                <i class="ph ph-arrows-clockwise"></i>
                            </button>
                        </div>
                        <small class="text-muted">Token yang harus dimasukkan peserta untuk memulai ujian</small>
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" name="status" value="published"
                                    class="btn btn-primary btn-lg">
                                <i class="ph-duotone ph-paper-plane-tilt me-2"></i>Publish Ujian
                            </button>
                            <button type="submit" name="status" value="draft"
                                    class="btn btn-outline-secondary">
                                <i class="ph-duotone ph-floppy-disk me-2"></i>Simpan sebagai Draft
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <h6 class="alert-heading"><i class="ph ph-info me-2"></i>Catatan</h6>
                            <small>
                                <strong>Draft:</strong> Hanya Anda yang dapat melihat ujian<br>
                                <strong>Published:</strong> Peserta dapat melihat dan mengikuti ujian<br><br>
                                Setelah membuat ujian, Anda dapat langsung menambahkan soal-soal ujian.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    function generateToken() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let token = '';
        for (let i = 0; i < 8; i++) {
            token += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('access_token').value = token;
    }

    function toggleSchedule(show) {
        document.getElementById('schedule-fields').style.display = show ? 'block' : 'none';
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');

        if (!show) {
            startTimeInput.removeAttribute('required');
            endTimeInput.removeAttribute('required');
            // Hide duration warning for flexible exams
            document.getElementById('duration-warning').classList.add('d-none');
        } else {
            startTimeInput.setAttribute('required', 'required');
            endTimeInput.setAttribute('required', 'required');
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
