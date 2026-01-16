@extends('layouts.app')

@section('title', 'Tambah Mata Pelajaran')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Mata Pelajaran Baru</h1>
        <a href="{{ route('admin.courses.index') }}" class="text-indigo-600 hover:text-indigo-900">
            &larr; Kembali
        </a>
    </div>
@endsection

@section('content')
    <form action="{{ route('admin.courses.store') }}" method="POST" class="max-w-2xl">
        @csrf
        
        <div class="bg-white shadow rounded-lg divide-y divide-gray-200">
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode *</label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}"
                               placeholder="e.g., IF101"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono @error('code') border-red-500 @enderror"
                               required>
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="credits" class="block text-sm font-medium text-gray-700 mb-1">SKS</label>
                        <input type="number" name="credits" id="credits" value="{{ old('credits', 3) }}"
                               min="1" max="6"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Pelajaran *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Deskripsi singkat mata pelajaran...">{{ old('description') }}</textarea>
                </div>
                
                <div>
                    <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-1">Guru Pengampu</label>
                    <select name="teacher_id" id="teacher_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Pilih Guru</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }} ({{ $teacher->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Siswa Peserta</label>
                    <div class="border border-gray-300 rounded-lg max-h-60 overflow-y-auto">
                        @foreach($students as $student)
                            <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="students[]" value="{{ $student->id }}"
                                       {{ in_array($student->id, old('students', [])) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <span class="ml-3">
                                    <span class="text-sm text-gray-900">{{ $student->name }}</span>
                                    <span class="text-sm text-gray-500">({{ $student->email }})</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Pilih siswa yang mengikuti mata pelajaran ini</p>
                </div>
            </div>
            
            <div class="p-6 bg-gray-50 flex justify-end space-x-4">
                <a href="{{ route('admin.courses.index') }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Simpan
                </button>
            </div>
        </div>
    </form>
@endsection
