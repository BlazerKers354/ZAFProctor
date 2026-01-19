@extends('layouts.admin')

@section('title', 'Kelola Kelas')
@section('page-title', 'Kelola Kelas')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Kelola Kelas</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Kelas</li>
                    </ul>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
                        <i class="ph-duotone ph-plus me-2"></i>Tambah Kelas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Classes Table -->
    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Tingkat</th>
                            <th>Wali Kelas</th>
                            <th>Jumlah Siswa</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $class)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm rounded me-3" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); display: flex; align-items: center; justify-content: center;">
                                            <span class="text-white f-12 fw-bold">{{ $class->name }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 f-14">Kelas {{ $class->name }}</h6>
                                            @if($class->description)
                                                <small class="text-muted">{{ $class->description }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">Tingkat {{ $class->grade_level }}</span>
                                </td>
                                <td>
                                    @if($class->homeroomTeacher)
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $class->homeroomTeacher->avatar_url }}" alt="" class="avatar avatar-sm avatar-circle me-2">
                                            <small>{{ $class->homeroomTeacher->name }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted f-12">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary">{{ $class->students_count }} siswa</span>
                                </td>
                                <td>
                                    @if($class->is_active)
                                        <span class="badge badge-soft-success">
                                            <i class="ph ph-check-circle me-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge badge-soft-secondary">
                                            <i class="ph ph-minus-circle me-1"></i>Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-sm btn-light-primary" title="Lihat">
                                        <i class="ph-duotone ph-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-sm btn-light-warning" title="Edit">
                                        <i class="ph-duotone ph-pencil-simple"></i>
                                    </a>
                                    <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">
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
                                            <i class="ph-duotone ph-chalkboard-teacher"></i>
                                        </div>
                                        <h6>Belum ada data kelas</h6>
                                        <p class="text-muted mb-3">Mulai dengan menambahkan kelas baru</p>
                                        <a href="{{ route('admin.classes.create') }}" class="btn btn-primary btn-sm">
                                            <i class="ph-duotone ph-plus me-1"></i>Tambah Kelas
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($classes->hasPages())
            <div class="card-footer">
                {{ $classes->links() }}
            </div>
        @endif
    </div>
@endsection
