@extends('layouts.admin')

@section('title', 'Edit Pengguna')
@section('page-title', 'Edit Pengguna')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Edit Pengguna: {{ $user->name }}</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph ph-house"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Pengguna</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph ph-pencil-simple me-2"></i>Edit Informasi Pengguna
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" required
                                       value="{{ old('name', $user->name) }}"
                                       placeholder="Masukkan nama lengkap"
                                       class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" required
                                       value="{{ old('email', $user->email) }}"
                                       placeholder="email@example.com"
                                       class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <input type="text" name="phone" id="phone"
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="08xxxxxxxxxx"
                                       class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role_id" id="role_id" required
                                        class="form-select @error('role_id') is-invalid @enderror">
                                    <option value="">Pilih Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="student_id" class="form-label">NIS/NIP</label>
                                <input type="text" name="student_id" id="student_id"
                                       value="{{ old('student_id', $user->student_id) }}"
                                       placeholder="Nomor Induk"
                                       class="form-control @error('student_id') is-invalid @enderror">
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <hr class="my-3">
                                <p class="text-muted small mb-3">Kosongkan field password jika tidak ingin mengubah</p>
                            </div>

                            <div class="col-md-12">
                                <label for="current_password" class="form-label">Password Lama</label>
                                <input type="password" name="current_password" id="current_password"
                                       placeholder="Masukkan password lama untuk verifikasi"
                                       class="form-control @error('current_password') is-invalid @enderror">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Wajib diisi jika ingin mengganti password baru</small>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" name="password" id="password"
                                       placeholder="Minimal 8 karakter"
                                       class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       placeholder="Ulangi password"
                                       class="form-control">
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_active" id="is_active" value="1"
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                           class="form-check-input">
                                    <label for="is_active" class="form-check-label">Pengguna Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="ph ph-x me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-floppy-disk me-1"></i>Update
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
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl mb-3" style="width: 80px; height: 80px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);">
                            <span class="text-white fw-bold fs-2">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <span class="badge bg-light-{{ $user->role->name == 'admin' ? 'danger' : ($user->role->name == 'teacher' ? 'success' : 'primary') }}">
                            {{ ucfirst($user->role->name) }}
                        </span>
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Bergabung</span>
                            <span>{{ $user->created_at->format('d M Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Terakhir Update</span>
                            <span>{{ $user->updated_at->format('d M Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Status</span>
                            @if($user->is_active)
                                <span class="badge bg-light-success">Aktif</span>
                            @else
                                <span class="badge bg-light-danger">Nonaktif</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
            
            @if($user->id !== auth()->id())
            <div class="card border-danger">
                <div class="card-header bg-light-danger">
                    <h5 class="card-title mb-0">
                        <i class="ph ph-warning me-2"></i>Zona Berbahaya
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="ph ph-trash me-1"></i>Hapus Pengguna
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
