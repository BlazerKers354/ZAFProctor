@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900">Edit Kelas: {{ $class->name }}</h1>
@endsection

@section('content')
    <div class="max-w-2xl">
        <form action="{{ route('admin.classes.update', $class) }}" method="POST" class="bg-white shadow sm:rounded-lg">
            @csrf
            @method('PUT')
            
            <div class="px-4 py-5 sm:p-6 space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Kelas *</label>
                    <input type="text" name="name" id="name" required
                           value="{{ old('name', $class->name) }}"
                           placeholder="Contoh: 1A, 2B, 3C"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="grade_level" class="block text-sm font-medium text-gray-700">Tingkat Kelas *</label>
                    <select name="grade_level" id="grade_level" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('grade_level') border-red-500 @enderror">
                        <option value="">Pilih Tingkat</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('grade_level', $class->grade_level) == $i ? 'selected' : '' }}>Tingkat {{ $i }}</option>
                        @endfor
                    </select>
                    @error('grade_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="homeroom_teacher_id" class="block text-sm font-medium text-gray-700">Wali Kelas</label>
                    <select name="homeroom_teacher_id" id="homeroom_teacher_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Pilih Wali Kelas (opsional)</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('homeroom_teacher_id', $class->homeroom_teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('homeroom_teacher_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description', $class->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           {{ old('is_active', $class->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Kelas Aktif
                    </label>
                </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-3">
                <a href="{{ route('admin.classes.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Update
                </button>
            </div>
        </form>
    </div>
@endsection
