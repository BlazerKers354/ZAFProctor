<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZAFProctor') }} - Login & Register</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        /* Hide browser's default password reveal button */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear,
        input[type="password"]::-webkit-credentials-auto-fill-button,
        input[type="password"]::-webkit-clear-button {
            display: none !important;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 100vh;
            padding: 20px;
        }

        .brand {
            text-align: center;
            margin-bottom: 30px;
            color: #fff;
        }

        .brand h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .brand p {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .container {
            background-color: #fff;
            border-radius: 30px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.35);
            position: relative;
            overflow: hidden;
            width: 850px;
            max-width: 100%;
            min-height: 550px;
        }

        .container p {
            font-size: 14px;
            line-height: 20px;
            letter-spacing: 0.3px;
            margin: 20px 0;
        }

        .container span {
            font-size: 12px;
            color: #666;
        }

        .container a {
            color: #512da8;
            font-size: 13px;
            text-decoration: none;
            margin: 15px 0 10px;
            transition: color 0.3s;
        }

        .container a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .container button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            font-size: 12px;
            padding: 12px 50px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .container button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .container button.hidden {
            background: transparent;
            border-color: #fff;
            box-shadow: none;
        }

        .container button.hidden:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }

        .container form {
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 50px;
            height: 100%;
        }

        .container form h1,
        .sign-up-content h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .container input {
            background-color: #f5f5f5;
            border: 2px solid transparent;
            margin: 8px 0;
            padding: 12px 15px;
            font-size: 13px;
            border-radius: 10px;
            width: 100%;
            outline: none;
            transition: all 0.3s ease;
        }

        .container input:focus {
            border-color: #667eea;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .container input.error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .sign-in {
            left: 0;
            width: 50%;
            z-index: 2;
            opacity: 1;
            visibility: visible;
        }

        .container.active .sign-in {
            transform: translateX(100%);
            opacity: 0;
            visibility: hidden;
        }

        .sign-up {
            left: 0;
            width: 50%;
            opacity: 0;
            visibility: hidden;
            z-index: 1;
        }

        .container.active .sign-up {
            transform: translateX(100%);
            opacity: 1;
            visibility: visible;
            z-index: 5;
            animation: move 0.6s;
        }

        @keyframes move {
            0%, 49.99% {
                opacity: 0;
                z-index: 1;
            }
            50%, 100% {
                opacity: 1;
                z-index: 5;
            }
        }

        .social-icons {
            margin: 15px 0;
        }

        .social-icons a {
            border: 1px solid #ddd;
            border-radius: 50%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0 5px;
            width: 42px;
            height: 42px;
            color: #333;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-color: transparent;
            transform: translateY(-3px);
        }

        .toggle-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: all 0.6s ease-in-out;
            border-radius: 150px 0 0 100px;
            z-index: 1000;
        }

        .container.active .toggle-container {
            transform: translateX(-100%);
            border-radius: 0 150px 100px 0;
        }

        .toggle {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            color: #fff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: all 0.6s ease-in-out;
        }

        .container.active .toggle {
            transform: translateX(50%);
        }

        .toggle-panel {
            position: absolute;
            width: 50%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 30px;
            text-align: center;
            top: 0;
            transform: translateX(0);
            transition: all 0.6s ease-in-out;
        }

        .toggle-panel h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .toggle-panel p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .toggle-panel .icon-box {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .toggle-panel .icon-box i {
            font-size: 35px;
        }

        .toggle-left {
            transform: translateX(-200%);
        }

        .container.active .toggle-left {
            transform: translateX(0);
        }

        .toggle-right {
            right: 0;
            transform: translateX(0);
        }

        .container.active .toggle-right {
            transform: translateX(200%);
        }

        .error-message {
            color: #ef4444;
            font-size: 11px;
            margin-top: -5px;
            margin-bottom: 5px;
            text-align: left;
            width: 100%;
        }

        .alert {
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 12px;
            width: 100%;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .input-group {
            width: 100%;
            position: relative;
        }

        .input-group .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px;
            margin: 0;
            box-shadow: none;
        }

        .input-group .toggle-password:hover {
            color: #667eea;
            transform: translateY(-50%);
            box-shadow: none;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            width: 100%;
            margin: 10px 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
            cursor: pointer;
        }

        .checkbox-group label {
            font-size: 12px;
            color: #666;
            cursor: pointer;
        }

        .form-scroll {
            max-height: 100%;
            overflow-y: auto;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 0;
        }

        /* Sign Up Content Styles */
        .sign-up-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 40px 50px;
            text-align: center;
        }

        .sign-up-content h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .sign-up-content p {
            color: #666;
            margin-bottom: 30px;
        }

        .register-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 15px 25px;
            color: white !important;
            text-decoration: none !important;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            border: none;
            cursor: pointer;
        }

        .register-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
            text-decoration: none !important;
        }

        .register-btn i {
            font-size: 18px;
        }

        .student-btn {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .teacher-btn {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
        }

        /* Registration Form Styles */
        .register-form {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-form form {
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-direction: column;
            padding: 20px 50px;
            height: 100%;
            width: 100%;
            overflow-y: auto;
        }

        .register-form form h1 {
            color: #333;
            margin-bottom: 5px;
        }

        .register-form form p {
            color: #666;
            margin-bottom: 15px;
            font-size: 13px;
        }

        .back-btn {
            align-self: flex-start;
            background: none !important;
            border: none !important;
            color: #667eea !important;
            font-size: 13px !important;
            padding: 5px 0 !important;
            margin-bottom: 10px !important;
            cursor: pointer;
            box-shadow: none !important;
            text-transform: none !important;
        }

        .back-btn:hover {
            color: #764ba2 !important;
            transform: none !important;
            box-shadow: none !important;
        }

        .select-input {
            background-color: #f5f5f5;
            border: 2px solid transparent;
            margin: 8px 0;
            padding: 12px 15px;
            font-size: 13px;
            border-radius: 10px;
            width: 100%;
            outline: none;
            transition: all 0.3s ease;
            font-family: 'Montserrat', sans-serif;
            color: #333;
            cursor: pointer;
        }

        .select-input:focus {
            border-color: #667eea;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .info-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 10px 15px;
            margin: 10px 0;
            width: 100%;
        }

        .info-box i {
            color: #f59e0b;
            font-size: 16px;
        }

        .info-box span {
            color: #92400e;
            font-size: 12px;
        }

        /* Animations */
        .fade-out {
            animation: fadeOut 0.3s ease forwards;
        }

        .fade-in {
            animation: fadeIn 0.3s ease forwards;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: scale(1);
            }
            to {
                opacity: 0;
                transform: scale(0.95);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                min-height: auto;
                width: 100%;
                border-radius: 20px;
            }

            .form-container {
                position: relative;
                width: 100%;
                height: auto;
                opacity: 1;
                transform: none !important;
            }

            .sign-up {
                display: none;
            }

            .container.active .sign-up {
                display: block;
            }

            .container.active .sign-in {
                display: none;
            }

            .toggle-container {
                display: none;
            }

            .container form {
                padding: 30px 25px;
            }

            .sign-up-content {
                padding: 30px 25px;
            }

            .register-form form {
                padding: 20px 25px;
            }

            .mobile-toggle {
                display: block;
                text-align: center;
                margin-top: 20px;
                padding-bottom: 20px;
            }

            .mobile-toggle span {
                color: #666;
            }

            .mobile-toggle a {
                color: #667eea;
                font-weight: 600;
            }
        }

        @media (min-width: 769px) {
            .mobile-toggle {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="brand">
        <h1>📝 ZAFProctor</h1>
        <p>Sistem Ujian Online dengan Camera Proctoring</p>
    </div>

    <div class="container" id="container">
        <!-- Sign Up Panel -->
        <div class="form-container sign-up">
            <!-- Step 1: Role Selection -->
            <div class="sign-up-content" id="role-selection">
                <h1>Buat Akun</h1>
                <p>Pilih jenis akun yang ingin Anda daftarkan</p>
                
                <button type="button" class="register-btn student-btn" onclick="showRegisterForm('student')">
                    <i class="fa-solid fa-graduation-cap"></i>
                    Daftar sebagai Siswa
                </button>
                
                <button type="button" class="register-btn teacher-btn" onclick="showRegisterForm('teacher')">
                    <i class="fa-solid fa-chalkboard-teacher"></i>
                    Daftar sebagai Guru
                </button>
                
                <div class="mobile-toggle">
                    <span>Sudah punya akun? </span>
                    <a href="#" onclick="toggleForm(false); return false;">Masuk di sini</a>
                </div>
            </div>

            <!-- Step 2: Student Registration Form -->
            <div class="register-form" id="student-form" style="display: none;">
                <form method="POST" action="{{ route('register.student') }}">
                    @csrf
                    <input type="hidden" name="_register" value="student">
                    <button type="button" class="back-btn" onclick="backToRoleSelection()">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </button>
                    <h1>Daftar Siswa</h1>
                    <p>Isi data untuk mendaftar sebagai siswa</p>

                    @if ($errors->any() && old('_register') === 'student')
                        <div class="alert alert-error" style="width: 100%;">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <ul style="margin: 0; padding-left: 20px; text-align: left;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <input type="text" name="name" placeholder="Nama Lengkap" value="{{ old('_register') === 'student' ? old('name') : '' }}" required>
                    <input type="email" name="email" placeholder="Email" value="{{ old('_register') === 'student' ? old('email') : '' }}" required>
                    <input type="text" name="student_id" placeholder="NIS (Nomor Induk Siswa)" value="{{ old('_register') === 'student' ? old('student_id') : '' }}">
                    
                    <select name="class_id" class="select-input">
                        <option value="">Pilih Kelas (Opsional)</option>
                        @php
                            $classes = \App\Models\SchoolClass::where('is_active', true)->orderBy('grade_level')->orderBy('name')->get();
                        @endphp
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ (old('_register') === 'student' && old('class_id') == $class->id) ? 'selected' : '' }}>Kelas {{ $class->name }}</option>
                        @endforeach
                    </select>

                    <div class="input-group">
                        <input type="password" name="password" id="student-password" placeholder="Password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('student-password', this)">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="student-password-confirm" placeholder="Konfirmasi Password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('student-password-confirm', this)">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    <button type="submit">Daftar Sekarang</button>
                    
                    <div class="mobile-toggle">
                        <span>Sudah punya akun? </span>
                        <a href="#" onclick="toggleForm(false); return false;">Masuk di sini</a>
                    </div>
                </form>
            </div>

            <!-- Step 2: Teacher Registration Form -->
            <div class="register-form" id="teacher-form" style="display: none;">
                <form method="POST" action="{{ route('register.teacher') }}">
                    @csrf
                    <input type="hidden" name="_register" value="teacher">
                    <button type="button" class="back-btn" onclick="backToRoleSelection()">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </button>
                    <h1>Daftar Guru</h1>
                    <p>Isi data untuk mendaftar sebagai guru</p>

                    @if ($errors->any() && old('_register') === 'teacher')
                        <div class="alert alert-error" style="width: 100%;">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <ul style="margin: 0; padding-left: 20px; text-align: left;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <input type="text" name="name" placeholder="Nama Lengkap" value="{{ old('_register') === 'teacher' ? old('name') : '' }}" required>
                    <input type="email" name="email" placeholder="Email" value="{{ old('_register') === 'teacher' ? old('email') : '' }}" required>
                    <input type="text" name="employee_id" placeholder="NIP (Nomor Induk Pegawai)" value="{{ old('_register') === 'teacher' ? old('employee_id') : '' }}">
                    <input type="text" name="phone" placeholder="No. Telepon" value="{{ old('_register') === 'teacher' ? old('phone') : '' }}">

                    <div class="input-group">
                        <input type="password" name="password" id="teacher-password" placeholder="Password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('teacher-password', this)">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="teacher-password-confirm" placeholder="Konfirmasi Password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('teacher-password-confirm', this)">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    <div class="info-box">
                        <i class="fa-solid fa-info-circle"></i>
                        <span>Akun guru memerlukan persetujuan admin sebelum dapat digunakan.</span>
                    </div>

                    <button type="submit">Daftar Sekarang</button>
                    
                    <div class="mobile-toggle">
                        <span>Sudah punya akun? </span>
                        <a href="#" onclick="toggleForm(false); return false;">Masuk di sini</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sign In Form -->
        <div class="form-container sign-in">
            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf
                <h1>Masuk</h1>

                @if (session('status'))
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any() && !old('_register'))
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> Email atau password salah.
                    </div>
                @endif

                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required
                       class="{{ $errors->has('email') && !old('_register') ? 'error' : '' }}">

                <div class="input-group">
                    <input type="password" name="password" id="login-password" placeholder="Password" required
                           class="{{ $errors->has('password') && !old('_register') ? 'error' : '' }}">
                    <button type="button" class="toggle-password" onclick="togglePassword('login-password', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Ingat saya</label>
                </div>

                <a href="#" onclick="showForgotPassword(); return false;">Lupa Password?</a>
                <button type="submit">Masuk</button>

                <div class="mobile-toggle">
                    <span>Belum punya akun? </span>
                    <a href="#" onclick="toggleForm(true); return false;">Daftar di sini</a>
                </div>
            </form>

            <!-- Forgot Password Form (Hidden by default) -->
            <form id="forgot-password-form" style="display: none;">
                @csrf
                <h1>Lupa Password?</h1>
                
                <div id="forgot-alert-success" class="alert alert-success" style="display: none;">
                    <i class="fa-solid fa-circle-check"></i> <span id="forgot-success-text"></span>
                </div>
                
                <div id="forgot-alert-error" class="alert alert-error" style="display: none;">
                    <i class="fa-solid fa-circle-exclamation"></i> <span id="forgot-error-text"></span>
                </div>

                <p style="text-align: center; color: #666; margin-bottom: 20px;">
                    Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
                </p>

                <input type="email" name="email" id="forgot-email" placeholder="Email" required>

                <button type="submit" id="forgot-submit-btn">
                    <span id="forgot-btn-text" style="color: white;">Kirim Link Reset</span>
                    <i id="forgot-btn-spinner" class="fa-solid fa-spinner fa-spin" style="display: none; margin-left: 8px;"></i>
                </button>

                <a href="#" onclick="showLoginForm(); return false;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Login
                </a>
            </form>
        </div>

        <!-- Toggle Container -->
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <div class="icon-box">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h1>Selamat Datang!</h1>
                    <p>Sudah punya akun? Masuk untuk mengakses ujian dan melihat hasil</p>
                    <button class="hidden" id="login" type="button">Masuk</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <div class="icon-box">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <h1>Halo, Teman!</h1>
                    <p>Daftar sekarang untuk mulai mengikuti ujian online dengan proctoring</p>
                    <button class="hidden" id="register" type="button">Daftar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        const registerBtn = document.getElementById('register');
        const loginBtn = document.getElementById('login');
        const roleSelection = document.getElementById('role-selection');
        const studentForm = document.getElementById('student-form');
        const teacherForm = document.getElementById('teacher-form');

        registerBtn.addEventListener('click', () => {
            container.classList.add("active");
        });

        loginBtn.addEventListener('click', () => {
            container.classList.remove("active");
            // Reset to role selection when going back to login
            backToRoleSelection();
        });

        function toggleForm(showRegister) {
            if (showRegister) {
                container.classList.add("active");
            } else {
                container.classList.remove("active");
                // Reset to role selection
                backToRoleSelection();
            }
        }

        function showRegisterForm(role) {
            // Fade out role selection
            roleSelection.classList.add('fade-out');
            
            setTimeout(() => {
                roleSelection.style.display = 'none';
                roleSelection.classList.remove('fade-out');
                
                if (role === 'student') {
                    studentForm.style.display = 'flex';
                    studentForm.classList.add('fade-in');
                } else if (role === 'teacher') {
                    teacherForm.style.display = 'flex';
                    teacherForm.classList.add('fade-in');
                }
                
                // Remove animation class after animation completes
                setTimeout(() => {
                    studentForm.classList.remove('fade-in');
                    teacherForm.classList.remove('fade-in');
                }, 300);
            }, 300);
        }

        function backToRoleSelection() {
            // Fade out current form
            const currentForm = studentForm.style.display === 'flex' ? studentForm : 
                               (teacherForm.style.display === 'flex' ? teacherForm : null);
            
            if (currentForm) {
                currentForm.classList.add('fade-out');
                
                setTimeout(() => {
                    currentForm.style.display = 'none';
                    currentForm.classList.remove('fade-out');
                    
                    roleSelection.style.display = 'flex';
                    roleSelection.classList.add('fade-in');
                    
                    setTimeout(() => {
                        roleSelection.classList.remove('fade-in');
                    }, 300);
                }, 300);
            } else {
                roleSelection.style.display = 'flex';
                studentForm.style.display = 'none';
                teacherForm.style.display = 'none';
            }
        }

        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Auto activate and show correct form if there are register errors
        @if(old('_register'))
            container.classList.add("active");
            @if(old('_register') === 'student')
                roleSelection.style.display = 'none';
                studentForm.style.display = 'flex';
            @elseif(old('_register') === 'teacher')
                roleSelection.style.display = 'none';
                teacherForm.style.display = 'flex';
            @endif
        @endif

        // Forgot Password Functions
        const loginForm = document.getElementById('login-form');
        const forgotPasswordForm = document.getElementById('forgot-password-form');

        function showForgotPassword() {
            loginForm.style.display = 'none';
            forgotPasswordForm.style.display = 'flex';
            document.getElementById('forgot-email').focus();
            // Reset alerts
            document.getElementById('forgot-alert-success').style.display = 'none';
            document.getElementById('forgot-alert-error').style.display = 'none';
        }

        function showLoginForm() {
            forgotPasswordForm.style.display = 'none';
            loginForm.style.display = 'flex';
            // Reset form
            forgotPasswordForm.reset();
            document.getElementById('forgot-alert-success').style.display = 'none';
            document.getElementById('forgot-alert-error').style.display = 'none';
        }

        // Handle forgot password form submission via AJAX
        forgotPasswordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('forgot-email').value;
            const submitBtn = document.getElementById('forgot-submit-btn');
            const btnText = document.getElementById('forgot-btn-text');
            const btnSpinner = document.getElementById('forgot-btn-spinner');
            const alertSuccess = document.getElementById('forgot-alert-success');
            const alertError = document.getElementById('forgot-alert-error');
            const successText = document.getElementById('forgot-success-text');
            const errorText = document.getElementById('forgot-error-text');
            
            // Reset alerts
            alertSuccess.style.display = 'none';
            alertError.style.display = 'none';
            
            // Show loading state
            submitBtn.disabled = true;
            btnText.textContent = 'Mengirim...';
            btnSpinner.style.display = 'inline-block';
            
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
                    alertSuccess.style.display = 'block';
                    forgotPasswordForm.reset();
                } else {
                    errorText.textContent = data.message || data.errors?.email?.[0] || 'Terjadi kesalahan. Silakan coba lagi.';
                    alertError.style.display = 'block';
                }
            } catch (error) {
                errorText.textContent = 'Terjadi kesalahan jaringan. Silakan coba lagi.';
                alertError.style.display = 'block';
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                btnText.textContent = 'Kirim Link Reset';
                btnSpinner.style.display = 'none';
            }
        });
    </script>
</body>
</html>
