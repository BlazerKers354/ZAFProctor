@extends('layouts.app')

@section('title', 'Kelola Soal')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Soal</h1>
            <p class="text-sm text-gray-500">{{ $exam->title }} - {{ $exam->course->name }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('teacher.exams.show', $exam) }}" class="text-indigo-600 hover:text-indigo-900">
                &larr; Kembali ke Ujian
            </a>
            <a href="{{ route('teacher.questions.create', $exam) }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Soal
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Summary -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6 text-sm">
                <span class="text-gray-500">Total: <span class="font-medium text-gray-900">{{ $exam->questions->count() }} soal</span></span>
                <span class="text-gray-500">Pilihan Ganda: <span class="font-medium text-gray-900">{{ $exam->questions->where('question_type', 'multiple_choice')->count() }}</span></span>
                <span class="text-gray-500">Essay: <span class="font-medium text-gray-900">{{ $exam->questions->where('question_type', 'essay')->count() }}</span></span>
                <span class="text-gray-500">Total Poin: <span class="font-medium text-gray-900">{{ $exam->questions->sum('points') }}</span></span>
            </div>
            
            @if($exam->questions->count() > 1)
                <button onclick="document.getElementById('reorder-form').classList.toggle('hidden')"
                        class="text-sm text-indigo-600 hover:text-indigo-900">
                    Atur Urutan
                </button>
            @endif
        </div>
    </div>
    
    <!-- Questions List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($exam->questions->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500 mb-4">Belum ada soal untuk ujian ini.</p>
                <a href="{{ route('teacher.questions.create', $exam) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    Tambah Soal Pertama
                </a>
            </div>
        @else
            <div class="divide-y divide-gray-200" id="questions-list">
                @foreach($exam->questions as $index => $question)
                    <div class="p-6 hover:bg-gray-50" id="question-{{ $question->id }}">
                        <div class="flex items-start">
                            <!-- Number & Type -->
                            <div class="flex-shrink-0 mr-4">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-medium">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $question->isMultipleChoice() ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $question->isMultipleChoice() ? 'Pilihan Ganda' : 'Essay' }}
                                    </span>
                                    <span class="text-sm text-gray-500">{{ $question->points }} poin</span>
                                </div>
                                
                                <p class="text-gray-900 mb-3">{{ Str::limit($question->question, 200) }}</p>
                                
                                @if($question->isMultipleChoice() && $question->options->isNotEmpty())
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        @foreach($question->options as $option)
                                            <div class="flex items-center {{ $option->is_correct ? 'text-green-700 font-medium' : 'text-gray-600' }}">
                                                <span class="mr-2">{{ $option->option_label }}.</span>
                                                <span>{{ Str::limit($option->option_text, 50) }}</span>
                                                @if($option->is_correct)
                                                    <span class="ml-1 text-green-600">✓</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex-shrink-0 ml-4 flex items-center space-x-2">
                                <a href="{{ route('teacher.questions.edit', [$exam, $question]) }}"
                                   class="p-2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('teacher.questions.destroy', [$exam, $question]) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
