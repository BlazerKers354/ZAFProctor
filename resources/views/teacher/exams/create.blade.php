@extends('layouts.app')

@section('title', 'Buat Ujian Baru')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Buat Ujian Baru</h1>
        <a href="{{ route('teacher.exams.index') }}" class="text-indigo-600 hover:text-indigo-900">
            &larr; Kembali
        </a>
    </div>
@endsection

@section('content')
    <form action="{{ route('teacher.exams.store') }}" method="POST" class="max-w-4xl">
        @csrf
        
        <div class="bg-white shadow rounded-lg divide-y divide-gray-200">
            <!-- Basic Info -->
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Ujian *</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror"
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran *</label>
                        <select name="course_id" id="course_id"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('course_id') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->code }} - {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">Durasi (menit) *</label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 60) }}"
                               min="5" max="300"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('duration_minutes') border-red-500 @enderror"
                               required>
                        @error('duration_minutes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="instructions" class="block text-sm font-medium text-gray-700 mb-1">Petunjuk Ujian</label>
                        <textarea name="instructions" id="instructions" rows="4"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Berikan petunjuk untuk peserta ujian...">{{ old('instructions') }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Schedule -->
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Jadwal Ujian</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai *</label>
                        <input type="datetime-local" name="start_time" id="start_time" 
                               value="{{ old('start_time') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('start_time') border-red-500 @enderror"
                               required>
                        @error('start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai *</label>
                        <input type="datetime-local" name="end_time" id="end_time" 
                               value="{{ old('end_time') }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('end_time') border-red-500 @enderror"
                               required>
                        @error('end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Proctoring Settings -->
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Pengaturan Proctoring</h2>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="require_camera" id="require_camera" value="1"
                                   {{ old('require_camera', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="require_camera" class="font-medium text-gray-700">Wajib Akses Kamera</label>
                            <p class="text-sm text-gray-500">Peserta harus mengizinkan akses webcam selama ujian</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="require_fullscreen" id="require_fullscreen" value="1"
                                   {{ old('require_fullscreen', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="require_fullscreen" class="font-medium text-gray-700">Wajib Mode Fullscreen</label>
                            <p class="text-sm text-gray-500">Peserta harus dalam mode fullscreen selama ujian</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="shuffle_questions" id="shuffle_questions" value="1"
                                   {{ old('shuffle_questions') ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="shuffle_questions" class="font-medium text-gray-700">Acak Urutan Soal</label>
                            <p class="text-sm text-gray-500">Urutan soal berbeda untuk setiap peserta</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label for="max_violations" class="block text-sm font-medium text-gray-700 mb-1">Maksimal Pelanggaran</label>
                            <input type="number" name="max_violations" id="max_violations" 
                                   value="{{ old('max_violations', 5) }}" min="1" max="20"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">Ujian akan otomatis dikumpulkan jika melebihi batas</p>
                        </div>
                        
                        <div>
                            <label for="passing_score" class="block text-sm font-medium text-gray-700 mb-1">Nilai Minimum Lulus (%)</label>
                            <input type="number" name="passing_score" id="passing_score" 
                                   value="{{ old('passing_score', 60) }}" min="0" max="100"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Access Token -->
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Token Akses</h2>
                
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="text" name="access_token" id="access_token" 
                               value="{{ old('access_token', strtoupper(Str::random(8))) }}"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono"
                               readonly>
                        <p class="mt-1 text-xs text-gray-500">Token yang harus dimasukkan peserta untuk memulai ujian</p>
                    </div>
                    <button type="button" onclick="generateToken()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Generate Baru
                    </button>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="p-6 bg-gray-50 flex justify-end space-x-4">
                <button type="submit" name="status" value="draft"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    Simpan sebagai Draft
                </button>
                <button type="submit" name="status" value="published"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Publish Ujian
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    function generateToken() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let token = '';
        for (let i = 0; i < 8; i++) {
            token += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('access_token').value = token;
    }
</script>
@endpush
