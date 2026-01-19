@extends('layouts.admin')

@section('title', 'Detail Mata Pelajaran')
@section('page-title', 'Detail Mata Pelajaran')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Detail Mata Pelajaran: {{ $course->name }}</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Mata Pelajaran</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $course->name }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Course Info -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ph-duotone ph-info me-2"></i>Informasi
                    </h5>
                    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-outline-primary">
                        <i class="ph-duotone ph-pencil-simple me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl mb-3" style="width: 80px; height: 80px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);">
                            <span class="text-white fw-bold fs-2">{{ strtoupper(substr($course->code, 0, 2)) }}</span>
                        </div>
                        <h4 class="mb-1">{{ $course->name }}</h4>
                        <span class="badge bg-secondary">{{ $course->code }}</span>
                        @if($course->is_active)
                            <span class="badge bg-light-success">Aktif</span>
                        @else
                            <span class="badge bg-light-danger">Tidak Aktif</span>
                        @endif
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="ph-duotone ph-user me-2"></i>Pengajar</span>
                            <span class="fw-medium">{{ $course->teacher->name ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="ph-duotone ph-users me-2"></i>Jumlah Siswa</span>
                            <span class="badge bg-primary">{{ $course->students->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="ph-duotone ph-exam me-2"></i>Jumlah Ujian</span>
                            <span class="badge bg-info">{{ $course->exams->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted"><i class="ph-duotone ph-calendar me-2"></i>Dibuat</span>
                            <span>{{ $course->created_at->format('d M Y') }}</span>
                        </li>
                    </ul>
                    
                    @if($course->description)
                        <div class="mt-3">
                            <p class="text-muted small mb-1">Deskripsi:</p>
                            <p class="mb-0">{{ $course->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="ph-duotone ph-arrow-left me-2"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Students List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ph-duotone ph-users me-2"></i>Daftar Siswa ({{ $course->students->count() }})
                    </h5>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="ph-duotone ph-plus me-1"></i>Tambah Siswa
                    </button>
                </div>
                <div class="card-body p-0">
                    @if($course->students->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Siswa</th>
                                        <th>Email</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($course->students as $index => $student)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);">
                                                        <span class="text-white fw-medium small">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $student->name }}</h6>
                                                        @if($student->student_id)
                                                            <small class="text-muted">{{ $student->student_id }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $student->email }}</span>
                                            </td>
                                            <td class="text-end">
                                                <form action="{{ route('admin.courses.remove-student', [$course, $student]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin mengeluarkan siswa ini dari mata pelajaran?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="ph-duotone ph-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ph-duotone ph-users text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 mb-1">Belum ada siswa</h5>
                            <p class="text-muted mb-0">Tambahkan siswa ke mata pelajaran ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Exams List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ph-duotone ph-exam me-2"></i>Daftar Ujian ({{ $course->exams->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($course->exams->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Judul Ujian</th>
                                        <th>Pembuat</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($course->exams as $exam)
                                        <tr>
                                            <td>
                                                <h6 class="mb-0">{{ $exam->title }}</h6>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $exam->creator->name ?? '-' }}</span>
                                            </td>
                                            <td>
                                                @if($exam->status == 'published')
                                                    <span class="badge bg-light-success">Published</span>
                                                @elseif($exam->status == 'draft')
                                                    <span class="badge bg-light-warning">Draft</span>
                                                @else
                                                    <span class="badge bg-light-secondary">{{ ucfirst($exam->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-muted small">{{ $exam->created_at->format('d M Y') }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ph-duotone ph-exam text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 mb-1">Belum ada ujian</h5>
                            <p class="text-muted mb-0">Belum ada ujian yang dibuat untuk mata pelajaran ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.courses.add-students', $course) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">
                            <i class="ph-duotone ph-user-plus me-2"></i>Tambah Siswa ke Mata Pelajaran
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $enrolledIds = $course->students->pluck('id')->toArray();
                            $availableStudents = \App\Models\User::byRole('student')->active()->whereNotIn('id', $enrolledIds)->get();
                        @endphp
                        
                        @if($availableStudents->count() > 0)
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Cari siswa..." id="searchStudent">
                            </div>
                            <p class="text-muted small">Pilih siswa yang akan ditambahkan:</p>
                            <div style="max-height: 400px; overflow-y: auto;">
                                <div class="list-group list-group-flush" id="studentList">
                                    @foreach($availableStudents as $student)
                                        <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 student-item" data-name="{{ strtolower($student->name) }}" data-email="{{ strtolower($student->email) }}">
                                            <input class="form-check-input flex-shrink-0" type="checkbox" name="student_ids[]" value="{{ $student->id }}">
                                            <div class="avatar avatar-sm" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);">
                                                <span class="text-white small">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $student->name }}</h6>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="ph-duotone ph-user-check text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3 mb-0">Tidak ada siswa yang tersedia untuk ditambahkan.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        @if($availableStudents->count() > 0)
                            <button type="submit" class="btn btn-primary">
                                <i class="ph-duotone ph-plus me-1"></i>Tambahkan
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('searchStudent')?.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.student-item').forEach(item => {
            const name = item.dataset.name;
            const email = item.dataset.email;
            if (name.includes(searchTerm) || email.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endpush
