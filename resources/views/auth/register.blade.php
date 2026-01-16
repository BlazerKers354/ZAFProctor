@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="mx-auto w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">
            Daftar Akun Baru
        </h2>
        <p class="mt-2 text-sm text-gray-500">Pilih jenis akun yang ingin Anda daftarkan</p>
    </div>

    <!-- Registration Options -->
    <div class="space-y-4">
        <!-- Student Registration -->
        <a href="{{ route('register.student.form') }}" 
           class="block p-6 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl hover:border-green-400 hover:shadow-lg transition-all duration-200 group">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-green-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-green-700">
                        Daftar sebagai Siswa
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Untuk siswa yang ingin mengikuti ujian online
                    </p>
                    <div class="mt-2 flex items-center text-xs text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="mr-1">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Akun langsung aktif
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 group-hover:text-green-500 group-hover:translate-x-1 transition-all duration-200">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
            </div>
        </a>

        <!-- Teacher Registration -->
        <a href="{{ route('register.teacher.form') }}" 
           class="block p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl hover:border-blue-400 hover:shadow-lg transition-all duration-200 group">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-blue-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-700">
                        Daftar sebagai Guru
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Untuk guru yang ingin membuat dan mengelola ujian
                    </p>
                    <div class="mt-2 flex items-center text-xs text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="mr-1">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        Perlu persetujuan admin
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all duration-200">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Divider -->
    <div class="relative mt-8">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white text-gray-500 font-medium">Sudah punya akun?</span>
        </div>
    </div>

    <!-- Login Link -->
    <div class="mt-6">
        <a href="{{ route('login') }}"
           class="w-full flex justify-center items-center py-3 px-4 border-2 border-gray-200 rounded-xl text-sm font-semibold text-gray-700 bg-white 
                  hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600
                  focus:outline-none focus:ring-4 focus:ring-indigo-100 
                  transition-all duration-200 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-gray-400">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                <polyline points="10 17 15 12 10 7"></polyline>
                <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
            Masuk ke Akun
        </a>
    </div>
@endsection
