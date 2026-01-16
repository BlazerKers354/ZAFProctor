@extends('layouts.app')

@section('title', 'Kelola Mata Pelajaran')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Kelola Mata Pelajaran</h1>
        <a href="{{ route('admin.courses.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Mata Pelajaran
        </a>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form action="{{ route('admin.courses.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari kode atau nama mata pelajaran..."
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Courses Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ujian</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($courses as $course)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-mono font-semibold bg-gray-100 text-gray-800 rounded">
                                {{ $course->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $course->name }}</div>
                            @if($course->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($course->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($course->teacher)
                                <div class="text-sm text-gray-900">{{ $course->teacher->name }}</div>
                            @else
                                <span class="text-sm text-gray-400">Belum ditentukan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $course->students_count }} siswa
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $course->exams_count }} ujian
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.courses.show', $course) }}" 
                               class="text-indigo-600 hover:text-indigo-900">Detail</a>
                            <a href="{{ route('admin.courses.edit', $course) }}" 
                               class="text-gray-600 hover:text-gray-900">Edit</a>
                            <form action="{{ route('admin.courses.destroy', $course) }}" 
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Belum ada mata pelajaran. <a href="{{ route('admin.courses.create') }}" class="text-indigo-600 hover:underline">Tambah mata pelajaran baru</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($courses->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
@endsection
