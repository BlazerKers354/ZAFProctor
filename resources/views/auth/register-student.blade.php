@extends('layouts.guest')

@section('title', 'Daftar Siswa')

@section('content')
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="mx-auto w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">
            Daftar Akun Siswa
        </h2>
        <p class="mt-2 text-sm text-gray-500">Lengkapi data berikut untuk mendaftar</p>
    </div>

    <form method="POST" action="{{ route('register.student') }}" class="space-y-5">
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
                          focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100
                          hover:border-gray-300
                          @error('name') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror">
            @error('name')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Student ID (NISN) -->
        <div class="space-y-2">
            <label for="student_id" class="block text-sm font-semibold text-gray-700">
                NISN / Nomor Induk <span class="text-red-500">*</span>
            </label>
            <input id="student_id" name="student_id" type="text" required
                   value="{{ old('student_id') }}"
                   placeholder="Masukkan NISN atau Nomor Induk Siswa"
                   class="block w-full px-4 py-3 border-2 border-gray-200 rounded-xl placeholder-gray-400 
                          transition-all duration-200 ease-in-out text-gray-900
                          focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100
                          hover:border-gray-300
                          @error('student_id') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror">
            @error('student_id')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Class -->
        <div class="space-y-2">
            <label for="class_id" class="block text-sm font-semibold text-gray-700">
                Kelas <span class="text-red-500">*</span>
            </label>
            <select id="class_id" name="class_id" required
                    class="block w-full px-4 py-3 border-2 border-gray-200 rounded-xl 
                           transition-all duration-200 ease-in-out text-gray-900
                           focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100
                           hover:border-gray-300
                           @error('class_id') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror">
                <option value="">-- Pilih Kelas --</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                        Kelas {{ $class->name }} (Tingkat {{ $class->grade_level }})
                    </option>
                @endforeach
            </select>
            @error('class_id')
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
                   placeholder="nama@email.com"
                   class="block w-full px-4 py-3 border-2 border-gray-200 rounded-xl placeholder-gray-400 
                          transition-all duration-200 ease-in-out text-gray-900
                          focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100
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
                          focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100
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
                          focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100
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
                          focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-100
                          hover:border-gray-300">
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit"
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl text-sm font-semibold text-white 
                           bg-gradient-to-r from-green-500 to-emerald-600 
                           hover:from-green-600 hover:to-emerald-700
                           focus:outline-none focus:ring-4 focus:ring-green-300 
                           shadow-lg hover:shadow-xl
                           transition-all duration-200 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                Daftar Sekarang
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
