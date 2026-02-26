@extends('layouts.admin')

@section('title', 'Tambah Kelas')
@section('page-title', 'Tambah Kelas')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Tambah Kelas Baru</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph ph-house"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Kelas</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.classes.store') }}" method="POST">
                @csrf
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-chalkboard me-2"></i>Informasi Kelas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" required
                                       value="{{ old('name') }}"
                                       placeholder="Contoh: 1A, 2B, 3C"
                                       class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="grade_level" class="form-label">Tingkat Kelas <span class="text-danger">*</span></label>
                                <select name="grade_level" id="grade_level" required
                                        class="form-select @error('grade_level') is-invalid @enderror">
                                    <option value="">Pilih Tingkat</option>
                                    @for($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}" {{ old('grade_level') == $i ? 'selected' : '' }}>Tingkat {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('grade_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="homeroom_teacher_id" class="form-label">Wali Kelas</label>
                                <select name="homeroom_teacher_id" id="homeroom_teacher_id" class="form-select">
                                    <option value="">Pilih Wali Kelas (opsional)</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('homeroom_teacher_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('homeroom_teacher_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea name="description" id="description" rows="3"
                                          class="form-control"
                                          placeholder="Deskripsi singkat kelas...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                                           class="form-check-input">
                                    <label for="is_active" class="form-check-label">Kelas Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="ph ph-x me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-floppy-disk me-1"></i>Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ph ph-info me-2"></i>Informasi
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted f-14 mb-3">Buat kelas baru untuk mengelompokkan siswa.</p>
                    <ul class="list-unstyled mb-0 f-14">
                        <li class="mb-2"><i class="ph ph-check-circle text-success me-2"></i>Nama kelas harus unik</li>
                        <li class="mb-2"><i class="ph ph-check-circle text-success me-2"></i>Pilih tingkat sesuai jenjang</li>
                        <li class="mb-2"><i class="ph ph-check-circle text-success me-2"></i>Wali kelas bersifat opsional</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
