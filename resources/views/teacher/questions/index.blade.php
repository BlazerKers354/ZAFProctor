@extends('layouts.teacher')

@section('title', 'Kelola Soal')
@section('page-title', 'Kelola Soal')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="page-header-title">
                                <h5 class="m-b-10">Kelola Soal - {{ $exam->title }}</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="ph-duotone ph-house"></i></a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.index') }}">Ujian</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('teacher.exams.show', $exam) }}">{{ Str::limit($exam->title, 20) }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Soal</li>
                            </ul>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-outline-secondary">
                                <i class="ph ph-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ph ph-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ph ph-warning me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h6 class="alert-heading"><i class="ph ph-warning-circle me-2"></i>Beberapa Baris Gagal Diimport</h6>
            <ul class="mb-0">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="{{ route('teacher.questions.create', $exam) }}" class="btn btn-primary w-100">
                        <i class="ph ph-plus-circle me-2"></i>Tambah Soal Manual
                    </a>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="ph ph-file-arrow-up me-2"></i>Import dari CSV
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('teacher.questions.download-template', $exam) }}" class="btn btn-info w-100">
                        <i class="ph ph-download-simple me-2"></i>Download Template CSV
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('teacher.questions.export', $exam) }}" class="btn btn-outline-secondary w-100">
                        <i class="ph ph-file-csv me-2"></i>Export Soal ke CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="border-end">
                        <h3 class="text-primary mb-1">{{ $questions->count() }}</h3>
                        <p class="text-muted mb-0 small">Total Soal</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-end">
                        <h3 class="text-success mb-1">{{ $questions->where('type', 'multiple_choice')->count() }}</h3>
                        <p class="text-muted mb-0 small">Pilihan Ganda</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-end">
                        <h3 class="text-info mb-1">{{ $questions->where('type', 'essay')->count() }}</h3>
                        <p class="text-muted mb-0 small">Essay</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <h3 class="text-warning mb-1">{{ $questions->sum('points') }}</h3>
                    <p class="text-muted mb-0 small">Total Poin</p>
                </div>
            </div>
        </div>
    </div>

    @if($questions->isEmpty())
        <!-- Empty State -->
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="ph-duotone ph-clipboard-text text-muted" style="font-size: 80px; opacity: 0.3;"></i>
                </div>
                <h4 class="mb-2">Belum Ada Soal</h4>
                <p class="text-muted mb-4">Mulai tambahkan soal untuk ujian ini dengan cara:</p>
                <div class="row justify-content-center g-3">
                    <div class="col-md-4">
                        <div class="card border border-primary">
                            <div class="card-body">
                                <i class="ph-duotone ph-pencil-simple text-primary mb-3" style="font-size: 48px;"></i>
                                <h6>Tambah Manual</h6>
                                <p class="text-muted small mb-3">Buat soal satu per satu dengan form yang mudah digunakan</p>
                                <a href="{{ route('teacher.questions.create', $exam) }}" class="btn btn-sm btn-primary">Mulai</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border border-success">
                            <div class="card-body">
                                <i class="ph-duotone ph-file-csv text-success mb-3" style="font-size: 48px;"></i>
                                <h6>Import CSV</h6>
                                <p class="text-muted small mb-3">Upload banyak soal sekaligus dari file CSV</p>
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#importModal">Import</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Questions List -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ph-duotone ph-list-numbers text-primary me-2"></i>Daftar Soal
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSelected()" id="deleteSelectedBtn" style="display: none;">
                            <i class="ph ph-trash me-1"></i>Hapus Terpilih (<span id="selectedCount">0</span>)
                        </button>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            <label class="form-check-label" for="selectAll">Pilih Semua</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">
                                    <input type="checkbox" id="selectAllHeader" onchange="toggleSelectAll()">
                                </th>
                                <th width="60" class="text-center">No</th>
                                <th>Pertanyaan</th>
                                <th width="150">Tipe</th>
                                <th width="100" class="text-center">Poin</th>
                                <th width="200" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questions as $index => $question)
                                <tr id="question-row-{{ $question->id }}" class="question-row">
                                    <td class="text-center">
                                        <input type="checkbox" class="question-checkbox" value="{{ $question->id }}" onchange="updateSelectedCount()">
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light-primary">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="question-preview">
                                            <p class="mb-1 fw-semibold">{{ Str::limit($question->question, 100) }}</p>
                                            @if($question->type === 'multiple_choice' && $question->options->isNotEmpty())
                                                <div class="options-preview mt-2">
                                                    <div class="row g-2">
                                                        @foreach($question->options->take(4) as $option)
                                                            <div class="col-md-6">
                                                                <small class="{{ $option->is_correct ? 'text-success fw-semibold' : 'text-muted' }}">
                                                                    <i class="ph {{ $option->is_correct ? 'ph-check-circle-fill' : 'ph-circle' }} me-1"></i>
                                                                    {{ $option->option_label }}. {{ Str::limit($option->option_text, 40) }}
                                                                </small>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($question->type === 'multiple_choice')
                                            <span class="badge bg-light-success">
                                                <i class="ph ph-check-square me-1"></i>Pilihan Ganda
                                            </span>
                                        @else
                                            <span class="badge bg-light-info">
                                                <i class="ph ph-text-aa me-1"></i>Essay
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light-warning fs-6">{{ $question->points }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-light-info" onclick="viewQuestion({{ $question->id }})" title="Lihat Detail">
                                                <i class="ph ph-eye"></i>
                                            </button>
                                            <a href="{{ route('teacher.questions.edit', [$exam, $question]) }}" class="btn btn-light-warning" title="Edit">
                                                <i class="ph ph-pencil-simple"></i>
                                            </a>
                                            <button type="button" class="btn btn-light-primary" onclick="duplicateQuestion({{ $question->id }})" title="Duplikat">
                                                <i class="ph ph-copy"></i>
                                            </button>
                                            <button type="button" class="btn btn-light-danger" onclick="deleteQuestion({{ $question->id }})" title="Hapus">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ph-duotone ph-file-arrow-up text-success me-2"></i>Import Soal dari CSV
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('teacher.questions.import', $exam) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="ph ph-info me-2"></i>Panduan Import</h6>
                            <ol class="mb-0 ps-3">
                                <li>Download template CSV terlebih dahulu</li>
                                <li>Isi data soal sesuai format pada template</li>
                                <li>Simpan file dalam format CSV (UTF-8)</li>
                                <li>Upload file CSV yang sudah diisi</li>
                            </ol>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">File CSV</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                            <small class="text-muted">Format file: .csv (maksimal 5MB)</small>
                        </div>

                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="ph ph-file-text me-2"></i>Format CSV yang Diperlukan:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>type</th>
                                                <th>question</th>
                                                <th>points</th>
                                                <th>option_a</th>
                                                <th>option_b</th>
                                                <th>option_c</th>
                                                <th>option_d</th>
                                                <th>correct_answer</th>
                                                <th>explanation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><small>multiple_choice</small></td>
                                                <td><small>Apa ibukota Indonesia?</small></td>
                                                <td><small>10</small></td>
                                                <td><small>Jakarta</small></td>
                                                <td><small>Bandung</small></td>
                                                <td><small>Surabaya</small></td>
                                                <td><small>Medan</small></td>
                                                <td><small>A</small></td>
                                                <td><small>Jakarta adalah...</small></td>
                                            </tr>
                                            <tr>
                                                <td><small>essay</small></td>
                                                <td><small>Jelaskan...</small></td>
                                                <td><small>20</small></td>
                                                <td><small>-</small></td>
                                                <td><small>-</small></td>
                                                <td><small>-</small></td>
                                                <td><small>-</small></td>
                                                <td><small>-</small></td>
                                                <td><small>Penjelasan...</small></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <strong>Catatan:</strong>
                                        <ul class="mb-0">
                                            <li>Untuk soal pilihan ganda, isi option_a sampai option_d (atau lebih)</li>
                                            <li>Untuk correct_answer, isi dengan huruf (A, B, C, D, dst)</li>
                                            <li>Untuk soal essay, kosongkan kolom option dan correct_answer atau isi dengan tanda "-"</li>
                                            <li>Kolom explanation bersifat opsional</li>
                                        </ul>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ph ph-upload me-2"></i>Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Question Detail Modal -->
    <div class="modal fade" id="questionDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="questionDetailContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll').checked || document.getElementById('selectAllHeader').checked;
        document.querySelectorAll('.question-checkbox').forEach(checkbox => {
            checkbox.checked = selectAll;
        });
        document.getElementById('selectAll').checked = selectAll;
        document.getElementById('selectAllHeader').checked = selectAll;
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.question-checkbox:checked');
        const count = selected.length;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('deleteSelectedBtn').style.display = count > 0 ? 'block' : 'none';
    }

    function deleteSelected() {
        const selected = Array.from(document.querySelectorAll('.question-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) return;
        
        if (confirm(`Apakah Anda yakin ingin menghapus ${selected.length} soal yang dipilih?`)) {
            fetch('{{ route('teacher.questions.delete-multiple', $exam) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ question_ids: selected })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Terjadi kesalahan saat menghapus soal');
                }
            });
        }
    }

    function deleteQuestion(id) {
        if (confirm('Apakah Anda yakin ingin menghapus soal ini?')) {
            fetch(`{{ route('teacher.questions.index', $exam) }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`question-row-${id}`).remove();
                    location.reload();
                } else {
                    alert('Terjadi kesalahan saat menghapus soal');
                }
            });
        }
    }

    function duplicateQuestion(id) {
        if (confirm('Duplikat soal ini?')) {
            fetch('{{ route('teacher.questions.duplicate', $exam) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ question_id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Terjadi kesalahan saat menduplikat soal');
                }
            });
        }
    }

    function viewQuestion(id) {
        const modal = new bootstrap.Modal(document.getElementById('questionDetailModal'));
        modal.show();
        
        fetch(`{{ route('teacher.questions.index', $exam) }}/${id}/detail`)
            .then(response => response.json())
            .then(data => {
                let html = `
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge ${data.type === 'multiple_choice' ? 'bg-success' : 'bg-info'} fs-6">
                                ${data.type === 'multiple_choice' ? 'Pilihan Ganda' : 'Essay'}
                            </span>
                            <span class="badge bg-warning fs-6">${data.points} Poin</span>
                        </div>
                        <h5 class="mb-3">${data.question}</h5>
                `;
                
                if (data.type === 'multiple_choice' && data.options) {
                    html += '<div class="list-group mb-3">';
                    data.options.forEach(option => {
                        html += `
                            <div class="list-group-item ${option.is_correct ? 'list-group-item-success' : ''}">
                                <strong>${option.option_label}.</strong> ${option.option_text}
                                ${option.is_correct ? '<i class="ph-fill ph-check-circle float-end text-success"></i>' : ''}
                            </div>
                        `;
                    });
                    html += '</div>';
                }
                
                if (data.explanation) {
                    html += `
                        <div class="alert alert-info">
                            <h6><i class="ph ph-lightbulb me-2"></i>Penjelasan:</h6>
                            <p class="mb-0">${data.explanation}</p>
                        </div>
                    `;
                }
                
                html += '</div>';
                document.getElementById('questionDetailContent').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('questionDetailContent').innerHTML = '<div class="alert alert-danger">Gagal memuat detail soal</div>';
            });
    }
</script>
@endpush
