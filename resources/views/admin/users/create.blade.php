@extends('layouts.admin')

@section('title', 'Tambah Pengguna')
@section('page-title', 'Tambah Pengguna')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Tambah Pengguna Baru</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Pengguna</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-user-plus me-2"></i>Informasi Pengguna
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Masukkan nama lengkap"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="contoh@email.com"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12">
                                <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role_id" id="role_id"
                                        class="form-select @error('role_id') is-invalid @enderror"
                                        required>
                                    <option value="">Pilih Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Min. 8 karakter"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control"
                                       placeholder="Ulangi password"
                                       required>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_active" id="is_active" value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="is_active" class="form-check-label">Aktifkan pengguna</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="ph-duotone ph-x me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>Simpan
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
                    <p class="text-muted f-14 mb-3">Buat akun pengguna baru untuk mengakses sistem.</p>
                    <ul class="list-unstyled mb-0 f-14">
                        <li class="mb-2"><i class="ph ph-check-circle text-success me-2"></i>Email harus unik</li>
                        <li class="mb-2"><i class="ph ph-check-circle text-success me-2"></i>Password minimal 8 karakter</li>
                        <li class="mb-2"><i class="ph ph-check-circle text-success me-2"></i>Pilih role sesuai hak akses</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
