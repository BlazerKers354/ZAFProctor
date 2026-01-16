@extends('layouts.guest')

@section('title', 'Lupa Password')

@section('content')
    <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">Lupa Password?</h2>
    <p class="text-sm text-gray-600 text-center mb-6">
        Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
    </p>

    @if (session('status'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                Email
            </label>
            <div class="mt-1">
                <input id="email" name="email" type="email" autocomplete="email" required
                       value="{{ old('email') }}"
                       class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') border-red-500 @enderror">
            </div>
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kirim Link Reset Password
            </button>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
            &larr; Kembali ke halaman login
        </a>
    </p>
@endsection
