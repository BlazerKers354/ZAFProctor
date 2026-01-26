@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <!-- Container untuk kedua form dengan animasi -->
    <div id="auth-container" class="relative" style="min-height: 580px;">
        <!-- LOGIN FORM -->
        <div id="login-panel" class="transition-all duration-500 ease-in-out">
            <!-- Header dengan ikon -->
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                        <circle cx="16" cy="8" r="5"></circle>
                        <path d="M5 28a11 11 0 0 1 22 0"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Selamat Datang
                </h2>
                <p class="mt-2 text-sm text-gray-500">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <!-- Alert Success -->
            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200">
                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="text-green-500 mr-2 flex-shrink-0 mt-0.5">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-green-700">{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-gray-700">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                                <circle cx="12" cy="12" r="4"></circle>
                                <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"></path>
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl placeholder-gray-400 
                                      transition-all duration-200 ease-in-out text-gray-900
                                      focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100
                                      hover:border-gray-300
                                      @error('email') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror">
                    </div>
                    @error('email')
                        <div class="flex items-center mt-2 text-sm text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20" fill="currentColor" class="mr-1.5 flex-shrink-0">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-gray-700">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               placeholder="••••••••"
                               class="block w-full pl-11 pr-12 py-3 border-2 border-gray-200 rounded-xl placeholder-gray-400 
                                      transition-all duration-200 ease-in-out text-gray-900
                                      focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100
                                      hover:border-gray-300
                                      @error('password') border-red-400 focus:border-red-500 focus:ring-red-100 @enderror">
                        <!-- Toggle Password Visibility -->
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 hover:text-gray-600 hidden">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 hover:text-gray-600">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="flex items-center mt-2 text-sm text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20" fill="currentColor" class="mr-1.5 flex-shrink-0">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center cursor-pointer">
                        <input id="remember" name="remember" type="checkbox"
                               class="w-4 h-4 text-indigo-600 border-2 border-gray-300 rounded focus:ring-indigo-500 focus:ring-offset-0 cursor-pointer">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>

                    <button type="button" onclick="showForgotPassword()" 
                       class="text-sm font-medium text-indigo-600 hover:text-indigo-500 hover:underline">
                        Lupa password?
                    </button>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl text-sm font-semibold text-white 
                                   bg-gradient-to-r from-indigo-600 to-purple-600 
                                   hover:from-indigo-700 hover:to-purple-700
                                   focus:outline-none focus:ring-4 focus:ring-indigo-300 
                                   shadow-lg hover:shadow-xl
                                   transition-all duration-200 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        Masuk ke Akun
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative mt-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500 font-medium">Belum punya akun?</span>
                </div>
            </div>

            <!-- Register Link -->
            <div class="mt-6">
                <a href="{{ route('register') }}"
                   class="w-full flex justify-center items-center py-3 px-4 border-2 border-gray-200 rounded-xl text-sm font-semibold text-gray-700 bg-white 
                          hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600
                          focus:outline-none focus:ring-4 focus:ring-indigo-100 
                          transition-all duration-200 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-gray-400">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    Daftar Akun Baru
                </a>
            </div>

            <!-- Footer Text -->
            <p class="mt-8 text-center text-xs text-gray-400">
                Dengan masuk, Anda menyetujui 
                <a href="#" class="text-indigo-600 hover:underline">Ketentuan Layanan</a> dan 
                <a href="#" class="text-indigo-600 hover:underline">Kebijakan Privasi</a> kami.
            </p>
        </div>

        <!-- FORGOT PASSWORD FORM -->
        <div id="forgot-panel" class="absolute inset-0 transition-all duration-500 ease-in-out hidden bg-white">
            <!-- Header dengan ikon -->
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Lupa Password?
                </h2>
                <p class="mt-2 text-sm text-gray-500">Masukkan email Anda dan kami akan mengirimkan link reset password</p>
            </div>

            <!-- Success/Error Messages -->
            <div id="forgot-success" class="hidden mb-6 p-4 rounded-xl bg-green-50 border border-green-200">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="text-green-500 mr-2 flex-shrink-0 mt-0.5">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span id="forgot-success-text" class="text-sm text-green-700"></span>
                </div>
            </div>

            <div id="forgot-error" class="hidden mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" class="text-red-500 mr-2 flex-shrink-0 mt-0.5">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span id="forgot-error-text" class="text-sm text-red-700"></span>
                </div>
            </div>

            <form id="forgot-form" class="space-y-5">
                @csrf

                <!-- Email -->
                <div class="space-y-2">
                    <label for="forgot-email" class="block text-sm font-semibold text-gray-700">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <input id="forgot-email" name="email" type="email" autocomplete="email" required
                               placeholder="nama@email.com"
                               class="block w-full pl-11 pr-4 py-3 border-2 border-gray-200 rounded-xl placeholder-gray-400 
                                      transition-all duration-200 ease-in-out text-gray-900
                                      focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-100
                                      hover:border-gray-300">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" id="forgot-submit-btn"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl text-sm font-semibold text-white 
                                   bg-gradient-to-r from-amber-500 to-orange-600 
                                   hover:from-amber-600 hover:to-orange-700
                                   focus:outline-none focus:ring-4 focus:ring-amber-300 
                                   shadow-lg hover:shadow-xl
                                   transition-all duration-200 ease-in-out
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg id="forgot-btn-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                        <svg id="forgot-btn-spinner" class="hidden animate-spin mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="forgot-btn-text">Kirim Link Reset</span>
                    </button>
                </div>
            </form>

            <!-- Back to Login -->
            <div class="mt-8 text-center">
                <button type="button" onclick="showLogin()" 
                   class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Kembali ke halaman Login
                </button>
            </div>

            <!-- Additional Info -->
            <div class="mt-8 p-4 rounded-xl bg-blue-50 border border-blue-200">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500 mr-3 flex-shrink-0 mt-0.5">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <div class="text-sm text-blue-700">
                        <p class="font-semibold mb-1">Tidak menerima email?</p>
                        <ul class="list-disc list-inside text-blue-600 space-y-1">
                            <li>Periksa folder spam atau junk</li>
                            <li>Pastikan email yang dimasukkan benar</li>
                            <li>Tunggu beberapa menit dan coba lagi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            }
        }

        function showForgotPassword() {
            const loginPanel = document.getElementById('login-panel');
            const forgotPanel = document.getElementById('forgot-panel');
            
            // Hide login, show forgot
            loginPanel.classList.add('hidden');
            forgotPanel.classList.remove('hidden');
            
            // Focus on email input
            setTimeout(() => {
                document.getElementById('forgot-email').focus();
            }, 100);
        }

        function showLogin() {
            const loginPanel = document.getElementById('login-panel');
            const forgotPanel = document.getElementById('forgot-panel');
            
            // Hide forgot, show login
            forgotPanel.classList.add('hidden');
            loginPanel.classList.remove('hidden');
            
            // Reset form and messages
            document.getElementById('forgot-form').reset();
            document.getElementById('forgot-success').classList.add('hidden');
            document.getElementById('forgot-error').classList.add('hidden');
        }

        // Handle forgot password form submission via AJAX
        document.getElementById('forgot-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('forgot-email').value;
            const submitBtn = document.getElementById('forgot-submit-btn');
            const btnIcon = document.getElementById('forgot-btn-icon');
            const btnSpinner = document.getElementById('forgot-btn-spinner');
            const btnText = document.getElementById('forgot-btn-text');
            const successDiv = document.getElementById('forgot-success');
            const successText = document.getElementById('forgot-success-text');
            const errorDiv = document.getElementById('forgot-error');
            const errorText = document.getElementById('forgot-error-text');
            
            // Reset messages
            successDiv.classList.add('hidden');
            errorDiv.classList.add('hidden');
            
            // Show loading state
            submitBtn.disabled = true;
            btnIcon.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
            btnText.textContent = 'Mengirim...';
            
            try {
                const response = await fetch('{{ route("password.email") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email: email })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    successText.textContent = data.message || 'Link reset password telah dikirim ke email Anda!';
                    successDiv.classList.remove('hidden');
                    document.getElementById('forgot-form').reset();
                } else {
                    errorText.textContent = data.message || data.errors?.email?.[0] || 'Terjadi kesalahan. Silakan coba lagi.';
                    errorDiv.classList.remove('hidden');
                }
            } catch (error) {
                errorText.textContent = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                errorDiv.classList.remove('hidden');
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                btnIcon.classList.remove('hidden');
                btnSpinner.classList.add('hidden');
                btnText.textContent = 'Kirim Link Reset';
            }
        });
    </script>
@endsection
