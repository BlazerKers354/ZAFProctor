@extends('layouts.admin')

@section('title', 'Tambah Mata Pelajaran')
@section('page-title', 'Tambah Mata Pelajaran')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Tambah Mata Pelajaran Baru</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph ph-house"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Mata Pelajaran</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.courses.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-book-bookmark me-2"></i>Informasi Mata Pelajaran
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="code" class="form-label">Kode <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" value="{{ old('code') }}"
                                       placeholder="e.g., MTK-01"
                                       class="form-control font-monospace @error('code') is-invalid @enderror"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="credits" class="form-label">SKS</label>
                                <input type="number" name="credits" id="credits" value="{{ old('credits', 3) }}"
                                       min="1" max="6"
                                       class="form-control">
                            </div>
                            
                            <div class="col-md-12">
                                <label for="name" class="form-label">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                       placeholder="Masukkan nama mata pelajaran"
                                       class="form-control @error('name') is-invalid @enderror"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea name="description" id="description" rows="3"
                                          class="form-control"
                                          placeholder="Deskripsi singkat mata pelajaran...">{{ old('description') }}</textarea>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="teacher_id" class="form-label">Guru Pengampu</label>
                                <select name="teacher_id" id="teacher_id" class="form-select">
                                    <option value="">Pilih Guru</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }} ({{ $teacher->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Students Selection -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-users me-2"></i>Siswa Peserta
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($students->count() > 0)
                            <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                                @foreach($students as $student)
                                    <label class="list-group-item list-group-item-action d-flex align-items-center">
                                        <input type="checkbox" name="students[]" value="{{ $student->id }}"
                                               {{ in_array($student->id, old('students', [])) ? 'checked' : '' }}
                                               class="form-check-input me-3">
                                        <img src="{{ $student->avatar_url }}" alt="" class="rounded-circle me-3" width="32" height="32">
                                        <div>
                                            <h6 class="mb-0 f-14">{{ $student->name }}</h6>
                                            <small class="text-muted">{{ $student->email }}</small>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="ph ph-user-plus fs-1 d-block mb-2 opacity-50"></i>
                                <p class="mb-0">Belum ada siswa terdaftar</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">Pilih siswa yang mengikuti mata pelajaran ini</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-info me-2"></i>Informasi
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted f-14 mb-3">Tambah mata pelajaran baru ke sistem.</p>
                        <ul class="list-unstyled mb-0 f-14">
                            <li class="mb-2"><i class="ph ph-check-circle text-success me-2"></i>Kode harus unik</li>
                            <li class="mb-2"><i class="ph ph-check-circle text-success me-2"></i>Pilih guru pengampu</li>
                            <li class="mb-2"><i class="ph ph-check-circle text-success me-2"></i>Pilih siswa peserta</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ph ph-floppy-disk me-1"></i>Simpan Mata Pelajaran
                            </button>
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                                <i class="ph ph-x me-1"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
