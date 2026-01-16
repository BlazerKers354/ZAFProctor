@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('header')
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Administrator</h1>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Total Users</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_users'] }}</p>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Total Courses</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_courses'] }}</p>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Total Exams</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_exams'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Completed Attempts</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['completed_attempts'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-50 rounded-lg p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-blue-600">Siswa</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $stats['total_students'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-green-600">Guru</p>
                    <p class="text-2xl font-bold text-green-900">{{ $stats['total_teachers'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 rounded-lg p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-yellow-600">Ujian Aktif</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $stats['active_exams'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Users -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">User Terbaru</h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                    Lihat Semua
                </a>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($recentUsers as $user)
                    <div class="p-4 flex items-center">
                        <img class="h-10 w-10 rounded-full" src="{{ $user->avatar_url }}" alt="">
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $user->role->name === 'admin' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $user->role->name === 'teacher' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $user->role->name === 'student' ? 'bg-blue-100 text-blue-800' : '' }}">
                            {{ $user->role->display_name }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Exams -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Ujian Terbaru</h2>
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-900">
                    Lihat Semua
                </a>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($recentExams as $exam)
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $exam->title }}</p>
                                <p class="text-sm text-gray-500">{{ $exam->course->name }}</p>
                                <p class="text-xs text-gray-400">oleh {{ $exam->creator->name }}</p>
                            </div>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $exam->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $exam->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $exam->status === 'ongoing' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $exam->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                {{ ucfirst($exam->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
