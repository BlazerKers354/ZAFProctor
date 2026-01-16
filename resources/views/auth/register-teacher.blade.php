@extends('layouts.guest')

@section('title', 'Daftar Guru')

@section('content')
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="mx-auto w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">
            Daftar Akun Guru
        </h2>
        <p class="mt-2 text-sm text-gray-500">Lengkapi data berikut untuk mendaftar</p>
    </div>

    <!-- Notice -->
    <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200">
        <div class="flex items-start">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="text-amber-500 mr-3 mt-0.5 flex-shrink-0">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <h3 class="text-sm font-semibold text-amber-800">Perlu Persetujuan Admin</h3>
                <p class="text-sm text-amber-700 mt-1">
                    Setelah mendaftar, akun Anda akan direview oleh administrator. 
                    Anda akan menerima email notifikasi ketika akun sudah disetujui.
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('register.teacher') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div class="space-y-2">
            <label for="name" class="block text-sm font-semibold text-gray-700">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input id="name" name="name" type="text" autocomplete="name" required
                   value="{{ old('name') }}"
                   placeholder="Masukkan nama lengkap"
                   class="block w-full px-4 py-3 border-2 border-gray-200 rounded-xl placeholder-gray-400 
                          transition-all duration-200 ease-in-out text-gray-900
                          focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100
                          hover:border-gray-300
                          @error('name') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror">
            @error('name')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-semibold text-gray-700">
                Alamat Email <span class="text-red-500">*</span>
            </label>
            <input id="email" name="email" type="email" autocomplete="email" required
                   value="{{ old('email') }}"
                   placeholder="nama@sekolah.sch.id"
                   class="block w-full px-4 py-3 border-2 border-gray-200 rounded-xl placeholder-gray-400 
                          transition-all duration-200 ease-in-out text-gray-900
                          focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100
                          hover:border-gray-300
                          @error('email') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror">
            @error('email')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone (Optional) -->
        <div class="space-y-2">
            <label for="phone" class="block text-sm font-semibold text-gray-700">
                Nomor Telepon <span class="text-gray-400 text-xs">(opsional)</span>
            </label>
            <input id="phone" name="phone" type="tel"
                   value="{{ old('phone') }}"
                   placeholder="08xxxxxxxxxx"
                   class="block w-full px-4 py-3 border-2 border-gray-200 rounded-xl placeholder-gray-400 
                          transition-all duration-200 ease-in-out text-gray-900
                          focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100
                          hover:border-gray-300
                          @error('phone') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror">
            @error('phone')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="block text-sm font-semibold text-gray-700">
                Password <span class="text-red-500">*</span>
            </label>
            <input id="password" name="password" type="password" autocomplete="new-password" required
                   placeholder="Minimal 8 karakter"
                   class="block w-full px-4 py-3 border-2 border-gray-200 rounded-xl placeholder-gray-400 
                          transition-all duration-200 ease-in-out text-gray-900
                          focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100
                          hover:border-gray-300
                          @error('password') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror">
            @error('password')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">
                Konfirmasi Password <span class="text-red-500">*</span>
            </label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                   placeholder="Ulangi password"
                   class="block w-full px-4 py-3 border-2 border-gray-200 rounded-xl placeholder-gray-400 
                          transition-all duration-200 ease-in-out text-gray-900
                          focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100
                          hover:border-gray-300">
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit"
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl text-sm font-semibold text-white 
                           bg-gradient-to-r from-blue-500 to-indigo-600 
                           hover:from-blue-600 hover:to-indigo-700
                           focus:outline-none focus:ring-4 focus:ring-blue-300 
                           shadow-lg hover:shadow-xl
                           transition-all duration-200 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                Ajukan Pendaftaran
            </button>
        </div>
    </form>

    <!-- Back Link -->
    <div class="mt-6 text-center">
        <a href="{{ route('register') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Kembali ke pilihan pendaftaran
        </a>
    </div>
@endsection
