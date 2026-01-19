@extends('layouts.admin')

@section('title', 'Kelola Mata Pelajaran')
@section('page-title', 'Mata Pelajaran')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Kelola Mata Pelajaran</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Mata Pelajaran</li>
                    </ul>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                        <i class="ph-duotone ph-plus me-2"></i>Tambah Mata Pelajaran
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.courses.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Cari Mata Pelajaran</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent"><i class="ph-duotone ph-magnifying-glass"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Kode atau nama mata pelajaran..."
                                   class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="ph-duotone ph-magnifying-glass me-1"></i>Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                                <i class="ph-duotone ph-x me-1"></i>Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Guru</th>
                            <th>Siswa</th>
                            <th>Ujian</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark fw-medium font-monospace">{{ $course->code }}</span>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0 f-14">{{ $course->name }}</h6>
                                        @if($course->description)
                                            <small class="text-muted">{{ Str::limit($course->description, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($course->teacher)
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $course->teacher->avatar_url }}" alt="" class="avatar avatar-sm avatar-circle me-2">
                                            <small>{{ $course->teacher->name }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted f-12">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary">{{ $course->students_count }} siswa</span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-info">{{ $course->exams_count }} ujian</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-light-primary" title="Detail">
                                        <i class="ph-duotone ph-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-light-warning" title="Edit">
                                        <i class="ph-duotone ph-pencil-simple"></i>
                                    </a>
                                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light-danger" title="Hapus">
                                            <i class="ph-duotone ph-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="ph-duotone ph-books"></i>
                                        </div>
                                        <h6>Belum ada mata pelajaran</h6>
                                        <p class="text-muted mb-3">Mulai dengan menambahkan mata pelajaran baru</p>
                                        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm">
                                            <i class="ph-duotone ph-plus me-1"></i>Tambah Mata Pelajaran
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($courses->hasPages())
            <div class="card-footer">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
@endsection
