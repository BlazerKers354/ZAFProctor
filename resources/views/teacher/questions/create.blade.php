@extends('layouts.app')

@section('title', 'Tambah Soal')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Soal Baru</h1>
            <p class="text-sm text-gray-500">{{ $exam->title }}</p>
        </div>
        <a href="{{ route('teacher.questions.index', $exam) }}" class="text-indigo-600 hover:text-indigo-900">
            &larr; Kembali
        </a>
    </div>
@endsection

@section('content')
    <form action="{{ route('teacher.questions.store', $exam) }}" method="POST" enctype="multipart/form-data" 
          class="max-w-4xl" x-data="questionForm()">
        @csrf
        
        <div class="bg-white shadow rounded-lg divide-y divide-gray-200">
            <!-- Question Type -->
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Tipe Soal *</label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="question_type" value="multiple_choice" 
                               x-model="questionType"
                               class="text-indigo-600 focus:ring-indigo-500" checked>
                        <span class="ml-2 text-gray-700">Pilihan Ganda</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="question_type" value="essay" 
                               x-model="questionType"
                               class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-gray-700">Essay</span>
                    </label>
                </div>
            </div>
            
            <!-- Question Text -->
            <div class="p-6">
                <label for="question" class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan *</label>
                <textarea name="question" id="question" rows="4"
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('question') border-red-500 @enderror"
                          placeholder="Tulis pertanyaan di sini..."
                          required>{{ old('question') }}</textarea>
                @error('question')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Question Image -->
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar (Opsional)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="question_image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                <span>Upload gambar</span>
                                <input id="question_image" name="question_image" type="file" class="sr-only" accept="image/*">
                            </label>
                            <p class="pl-1">atau drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF maksimal 2MB</p>
                    </div>
                </div>
            </div>
            
            <!-- Multiple Choice Options -->
            <div class="p-6" x-show="questionType === 'multiple_choice'">
                <label class="block text-sm font-medium text-gray-700 mb-3">Pilihan Jawaban *</label>
                
                <div class="space-y-4">
                    <template x-for="(option, index) in options" :key="index">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 pt-2">
                                <input type="radio" :name="'correct_option'" :value="index"
                                       x-model="correctOption"
                                       class="text-green-600 focus:ring-green-500">
                            </div>
                            <div class="flex-shrink-0 w-10 pt-2 text-center font-medium text-gray-700">
                                <span x-text="String.fromCharCode(65 + index)"></span>.
                            </div>
                            <div class="flex-1">
                                <input type="text" :name="'options[' + index + '][text]'" x-model="option.text"
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Tulis pilihan jawaban...">
                                <input type="hidden" :name="'options[' + index + '][label]'" :value="String.fromCharCode(65 + index)">
                            </div>
                            <button type="button" @click="removeOption(index)" 
                                    x-show="options.length > 2"
                                    class="flex-shrink-0 p-2 text-gray-400 hover:text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
                
                <input type="hidden" name="correct_option_index" :value="correctOption">
                
                <button type="button" @click="addOption()" 
                        x-show="options.length < 6"
                        class="mt-4 text-sm text-indigo-600 hover:text-indigo-900">
                    + Tambah Pilihan
                </button>
                
                <p class="mt-2 text-sm text-gray-500">Pilih radio button di sebelah kiri untuk menandai jawaban yang benar</p>
            </div>
            
            <!-- Essay Answer Key -->
            <div class="p-6" x-show="questionType === 'essay'">
                <label for="answer_key" class="block text-sm font-medium text-gray-700 mb-1">Kunci Jawaban (Opsional)</label>
                <textarea name="answer_key" id="answer_key" rows="4"
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                          placeholder="Tulis kunci jawaban atau pedoman penilaian untuk membantu grading...">{{ old('answer_key') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Kunci jawaban ini akan digunakan sebagai referensi saat menilai jawaban essay</p>
            </div>
            
            <!-- Explanation -->
            <div class="p-6">
                <label for="explanation" class="block text-sm font-medium text-gray-700 mb-1">Penjelasan (Opsional)</label>
                <textarea name="explanation" id="explanation" rows="3"
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                          placeholder="Tulis penjelasan jawaban yang benar...">{{ old('explanation') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Penjelasan ini akan ditampilkan ke peserta setelah ujian selesai (jika diizinkan)</p>
            </div>
            
            <!-- Points -->
            <div class="p-6">
                <label for="points" class="block text-sm font-medium text-gray-700 mb-1">Poin *</label>
                <div class="w-32">
                    <input type="number" name="points" id="points" 
                           value="{{ old('points', 10) }}" min="1" max="100"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           required>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="p-6 bg-gray-50 flex justify-end space-x-4">
                <a href="{{ route('teacher.questions.index', $exam) }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    Batal
                </a>
                <button type="submit" name="action" value="save"
                        class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Simpan
                </button>
                <button type="submit" name="action" value="save_and_new"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Simpan & Tambah Lagi
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    function questionForm() {
        return {
            questionType: '{{ old('question_type', 'multiple_choice') }}',
            correctOption: {{ old('correct_option_index', 0) }},
            options: [
                { text: '{{ old('options.0.text', '') }}' },
                { text: '{{ old('options.1.text', '') }}' },
                { text: '{{ old('options.2.text', '') }}' },
                { text: '{{ old('options.3.text', '') }}' }
            ],
            
            addOption() {
                if (this.options.length < 6) {
                    this.options.push({ text: '' });
                }
            },
            
            removeOption(index) {
                if (this.options.length > 2) {
                    this.options.splice(index, 1);
                    if (this.correctOption >= this.options.length) {
                        this.correctOption = this.options.length - 1;
                    }
                }
            }
        }
    }
</script>
@endpush
