@extends('layouts.admin')

@section('title', 'Detail Pengguna')
@section('page-title', 'Detail Pengguna')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Detail Pengguna: {{ $user->name }}</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Pengguna</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Info -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ph-duotone ph-user me-2"></i>Profil Pengguna
                    </h5>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                        <i class="ph-duotone ph-pencil-simple me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl mb-3" style="width: 100px; height: 100px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);">
                            <span class="text-white fw-bold" style="font-size: 2.5rem;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <span class="badge bg-light-{{ $user->role->name == 'admin' ? 'danger' : ($user->role->name == 'teacher' ? 'warning' : 'primary') }} mb-2">
                            {{ ucfirst($user->role->name) }}
                        </span>
                        <br>
                        @if($user->is_active)
                            <span class="badge bg-light-success">Aktif</span>
                        @else
                            <span class="badge bg-light-danger">Tidak Aktif</span>
                        @endif
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="ph-duotone ph-envelope me-2"></i>Email</span>
                            <span>{{ $user->email }}</span>
                        </li>
                        @if($user->phone)
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="ph-duotone ph-phone me-2"></i>Telepon</span>
                            <span>{{ $user->phone }}</span>
                        </li>
                        @endif
                        @if($user->student_id)
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="ph-duotone ph-identification-card me-2"></i>NIS/NIP</span>
                            <span>{{ $user->student_id }}</span>
                        </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="ph-duotone ph-calendar me-2"></i>Bergabung</span>
                            <span>{{ $user->created_at->format('d M Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="ph-duotone ph-check-circle me-2"></i>Verifikasi Email</span>
                            @if($user->email_verified_at)
                                <span class="badge bg-light-success">Terverifikasi</span>
                            @else
                                <span class="badge bg-light-warning">Belum Verifikasi</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="ph-duotone ph-arrow-left me-2"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @if($user->role->name == 'student')
                <!-- Student: Enrolled Courses -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-books me-2"></i>Mata Pelajaran yang Diikuti ({{ $user->enrolledCourses->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($user->enrolledCourses->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Nama Mata Pelajaran</th>
                                            <th>Pengajar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->enrolledCourses as $course)
                                            <tr>
                                                <td><span class="badge bg-secondary">{{ $course->code }}</span></td>
                                                <td>{{ $course->name }}</td>
                                                <td>{{ $course->teacher->name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="ph-duotone ph-books text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">Belum mengikuti mata pelajaran apapun</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Student: Exam Attempts -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-exam me-2"></i>Riwayat Ujian ({{ $user->examAttempts->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($user->examAttempts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ujian</th>
                                            <th>Status</th>
                                            <th>Nilai</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->examAttempts->take(10) as $attempt)
                                            <tr>
                                                <td>{{ $attempt->exam->title ?? 'Ujian dihapus' }}</td>
                                                <td>
                                                    @if($attempt->status == 'completed')
                                                        <span class="badge bg-light-success">Selesai</span>
                                                    @elseif($attempt->status == 'in_progress')
                                                        <span class="badge bg-light-warning">Berlangsung</span>
                                                    @else
                                                        <span class="badge bg-light-secondary">{{ ucfirst($attempt->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($attempt->score !== null)
                                                        <span class="fw-bold">{{ number_format($attempt->score, 1) }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $attempt->created_at->format('d M Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="ph-duotone ph-exam text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada riwayat ujian</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($user->role->name == 'teacher')
                <!-- Teacher: Taught Courses -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-chalkboard-teacher me-2"></i>Mata Pelajaran yang Diajar ({{ $user->taughtCourses->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($user->taughtCourses->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Nama Mata Pelajaran</th>
                                            <th>Siswa</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->taughtCourses as $course)
                                            <tr>
                                                <td><span class="badge bg-secondary">{{ $course->code }}</span></td>
                                                <td>{{ $course->name }}</td>
                                                <td><span class="badge bg-primary">{{ $course->students()->count() }}</span></td>
                                                <td>
                                                    @if($course->is_active)
                                                        <span class="badge bg-light-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-light-danger">Nonaktif</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="ph-duotone ph-chalkboard-teacher text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">Belum mengajar mata pelajaran apapun</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($user->role->name == 'admin')
                <!-- Admin Stats -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ph-duotone ph-shield-checkered me-2"></i>Statistik Administrator
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded text-center">
                                    <i class="ph-duotone ph-users text-primary" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-0">{{ \App\Models\User::count() }}</h3>
                                    <p class="text-muted mb-0">Total Pengguna</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded text-center">
                                    <i class="ph-duotone ph-books text-success" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-0">{{ \App\Models\Course::count() }}</h3>
                                    <p class="text-muted mb-0">Total Mata Pelajaran</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded text-center">
                                    <i class="ph-duotone ph-exam text-warning" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-0">{{ \App\Models\Exam::count() }}</h3>
                                    <p class="text-muted mb-0">Total Ujian</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded text-center">
                                    <i class="ph-duotone ph-graduation-cap text-info" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-0">{{ \App\Models\SchoolClass::count() }}</h3>
                                    <p class="text-muted mb-0">Total Kelas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
