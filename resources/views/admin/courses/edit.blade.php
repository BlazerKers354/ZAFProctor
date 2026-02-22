@extends('layouts.admin')

@section('title', 'Edit Mata Pelajaran')
@section('page-title', 'Edit Mata Pelajaran')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Edit Mata Pelajaran: {{ $course->name }}</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Mata Pelajaran</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.courses.update', $course) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-pencil-simple me-2"></i>Edit Informasi Mata Pelajaran
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="code" class="form-label">Kode <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" required
                                       value="{{ old('code', $course->code) }}"
                                       placeholder="Contoh: MTK01"
                                       class="form-control @error('code') is-invalid @enderror">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="name" class="form-label">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" required
                                       value="{{ old('name', $course->name) }}"
                                       placeholder="Contoh: Matematika Dasar"
                                       class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="teacher_id" class="form-label">Pengajar <span class="text-danger">*</span></label>
                                <select name="teacher_id" id="teacher_id" required
                                        class="form-select @error('teacher_id') is-invalid @enderror">
                                    <option value="">Pilih Pengajar</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $course->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teacher_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea name="description" id="description" rows="4"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Deskripsi singkat mata pelajaran...">{{ old('description', $course->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_active" id="is_active" value="1"
                                           {{ old('is_active', $course->is_active) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="is_active" class="form-check-label">Mata Pelajaran Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="ph-duotone ph-x me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ph-duotone ph-info me-2"></i>Informasi
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Jumlah Siswa</span>
                            <span class="badge badge-soft-primary">{{ $course->students()->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Jumlah Ujian</span>
                            <span class="badge badge-soft-info">{{ $course->exams()->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Dibuat</span>
                            <span>{{ $course->created_at->format('d M Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="ph-duotone ph-warning me-2"></i>Zona Berbahaya
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Menghapus mata pelajaran akan menghapus semua data terkait termasuk ujian dan nilai.</p>
                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini? Semua data terkait termasuk ujian akan dihapus.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="ph-duotone ph-trash me-1"></i>Hapus Mata Pelajaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
