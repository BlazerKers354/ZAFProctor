@extends('layouts.app')

@section('title', 'Kelola Ujian')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Kelola Ujian</h1>
        <a href="{{ route('teacher.exams.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Ujian Baru
        </a>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form action="{{ route('teacher.exams.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari ujian..."
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="w-48">
                <select name="course" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Sedang Berlangsung</option>
                    <option value="ended" {{ request('status') === 'ended' ? 'selected' : '' }}>Berakhir</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Exams Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ujian</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Soal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($exams as $exam)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $exam->title }}</div>
                            <div class="text-sm text-gray-500">{{ $exam->course->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $exam->start_time->format('d M Y, H:i') }}</div>
                            <div class="text-sm text-gray-500">{{ $exam->duration_minutes }} menit</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-900">{{ $exam->questions_count }} soal</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $exam->attempts_count }} / {{ $exam->course->students_count }}</div>
                            @if($exam->isActive())
                                <a href="{{ route('teacher.monitor.index', $exam) }}" 
                                   class="text-xs text-indigo-600 hover:underline">
                                    Monitor Live →
                                </a>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($exam->status === 'draft')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Draft
                                </span>
                            @elseif($exam->isActive())
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 animate-pulse">
                                    ● Berlangsung
                                </span>
                            @elseif(!$exam->hasStarted())
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Terjadwal
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Berakhir
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('teacher.exams.show', $exam) }}" 
                               class="text-indigo-600 hover:text-indigo-900">Detail</a>
                            <a href="{{ route('teacher.exams.edit', $exam) }}" 
                               class="text-gray-600 hover:text-gray-900">Edit</a>
                            <a href="{{ route('teacher.questions.index', $exam) }}" 
                               class="text-green-600 hover:text-green-900">Soal</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Belum ada ujian. <a href="{{ route('teacher.exams.create') }}" class="text-indigo-600 hover:underline">Buat ujian baru</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($exams->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $exams->links() }}
            </div>
        @endif
    </div>
@endsection
