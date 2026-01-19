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
                            
                            <div class="col-md-6">
                                <label for="duration" class="form-label">Durasi (menit) <span class="text-danger">*</span></label>
                                <input type="number" name="duration" id="duration" value="{{ old('duration', 60) }}"
                                       min="5" max="300"
                                       class="form-control @error('duration') is-invalid @enderror"
                                       required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="instructions" class="form-label">Petunjuk Ujian</label>
                                <textarea name="instructions" id="instructions" rows="4"
                                          class="form-control"
                                          placeholder="Berikan petunjuk untuk peserta ujian...">{{ old('instructions') }}</textarea>
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
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_time" id="start_time" 
                                       value="{{ old('start_time') }}"
                                       class="form-control @error('start_time') is-invalid @enderror"
                                       required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_time" id="end_time" 
                                       value="{{ old('end_time') }}"
                                       class="form-control @error('end_time') is-invalid @enderror"
                                       required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                    <input type="checkbox" name="require_camera" id="require_camera" value="1"
                                           {{ old('require_camera', true) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="require_camera" class="form-check-label fw-medium">Wajib Akses Kamera</label>
                                </div>
                                <small class="text-muted">Peserta harus mengizinkan akses webcam selama ujian</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="require_fullscreen" id="require_fullscreen" value="1"
                                           {{ old('require_fullscreen', true) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="require_fullscreen" class="form-check-label fw-medium">Wajib Mode Fullscreen</label>
                                </div>
                                <small class="text-muted">Peserta harus dalam mode fullscreen selama ujian</small>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" name="shuffle_questions" id="shuffle_questions" value="1"
                                           {{ old('shuffle_questions') ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="shuffle_questions" class="form-check-label fw-medium">Acak Urutan Soal</label>
                                </div>
                                <small class="text-muted">Urutan soal berbeda untuk setiap peserta</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="max_violations" class="form-label">Maksimal Pelanggaran</label>
                                <input type="number" name="max_violations" id="max_violations" 
                                       value="{{ old('max_violations', 5) }}" min="1" max="20"
                                       class="form-control">
                                <small class="text-muted">Ujian akan otomatis dikumpulkan jika melebihi batas</small>
                            </div>
                            
                            <div class="col-md-6">
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
</script>
@endpush
