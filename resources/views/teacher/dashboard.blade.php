@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Guru</h1>
@endsection

@section('content')
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Mata Pelajaran</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_courses'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Total Siswa</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_students'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Total Ujian</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_exams'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Ujian Aktif</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_exams'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Active Exams with Monitoring -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Ujian Berlangsung</h2>
                <a href="{{ route('teacher.exams.create') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                    + Buat Ujian Baru
                </a>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($activeExams as $exam)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ $exam->title }}</h3>
                                <p class="text-sm text-gray-500">{{ $exam->course->name }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $exam->attempts_count }} peserta
                                </p>
                            </div>
                            <a href="{{ route('teacher.exams.monitor', $exam) }}" 
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Monitor
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        Tidak ada ujian aktif saat ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Attempts -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Aktivitas Terakhir</h2>
            </div>
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($recentAttempts as $attempt)
                    <div class="p-4">
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full" src="{{ $attempt->user->avatar_url }}" alt="">
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $attempt->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $attempt->exam->title }}</p>
                            </div>
                            <div class="text-right">
                                @if($attempt->status === 'in_progress')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Sedang Mengerjakan
                                    </span>
                                @elseif($attempt->status === 'submitted' || $attempt->status === 'graded')
                                    <span class="text-sm font-medium {{ $attempt->is_passed ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($attempt->percentage, 1) }}%
                                    </span>
                                @endif
                                <p class="text-xs text-gray-400">
                                    @if($attempt->violation_count > 0)
                                        <span class="text-red-500">{{ $attempt->violation_count }} pelanggaran</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        Belum ada aktivitas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Courses Overview -->
    <div class="mt-8 bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Mata Pelajaran Saya</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            @forelse($courses as $course)
                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    <h3 class="font-medium text-gray-900">{{ $course->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $course->code }}</p>
                    <div class="mt-4 flex justify-between text-sm text-gray-500">
                        <span>{{ $course->students_count }} siswa</span>
                        <span>{{ $course->exams_count }} ujian</span>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center text-gray-500 py-8">
                    Belum ada mata pelajaran yang ditugaskan.
                </div>
            @endforelse
        </div>
    </div>
@endsection
