@extends('layouts.admin')

@section('title', 'Persetujuan Pendaftaran')
@section('page-title', 'Approval User')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Persetujuan Pendaftaran</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph ph-house"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Pengguna</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pending Approval</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card">
        @if($pendingUsers->count() > 0)
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ph ph-user-check me-2"></i>User Menunggu Persetujuan
                    <span class="badge bg-warning ms-2">{{ $pendingUsers->total() }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Tanggal Daftar</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingUsers as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm rounded-circle me-3" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); display: flex; align-items: center; justify-content: center;">
                                                <span class="text-white f-12 fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 f-14">{{ $user->name }}</h6>
                                                <span class="badge badge-soft-warning f-10">Pending</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light-{{ $user->role->name == 'teacher' ? 'success' : 'primary' }}">
                                            {{ ucfirst($user->role->name) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $user->created_at->format('d M Y H:i') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                <i class="ph ph-check me-1"></i>Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.reject', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menolak pendaftaran ini? Akun akan dihapus.')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" title="Tolak">
                                                <i class="ph ph-x me-1"></i>Tolak
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if($pendingUsers->hasPages())
                <div class="card-footer">
                    {{ $pendingUsers->links() }}
                </div>
            @endif
        @else
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="ph ph-check-circle text-success"></i>
                    </div>
                    <h6>Tidak ada pendaftaran yang menunggu</h6>
                    <p class="text-muted mb-0">Semua pendaftaran telah diproses.</p>
                </div>
            </div>
        @endif
    </div>
@endsection
