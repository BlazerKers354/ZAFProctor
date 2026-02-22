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
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated mesh gradient background */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(ellipse at 20% 50%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(255, 119, 115, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 40% 80%, rgba(59, 130, 246, 0.2) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(16, 185, 129, 0.15) 0%, transparent 50%);
            animation: meshMove 20s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes meshMove {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(2%, -3%) rotate(1deg); }
            50% { transform: translate(-1%, 2%) rotate(-1deg); }
            75% { transform: translate(3%, 1%) rotate(0.5deg); }
        }

        /* Floating geometric shapes */
        .geo-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .geo-shape {
            position: absolute;
            border: 1px solid rgba(255,255,255,0.06);
            animation: float 15s ease-in-out infinite;
        }

        .geo-shape:nth-child(1) {
            width: 300px;
            height: 300px;
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            top: 10%;
            left: -5%;
            animation-delay: 0s;
            animation-duration: 18s;
        }

        .geo-shape:nth-child(2) {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            bottom: 10%;
            right: 5%;
            animation-delay: -3s;
            animation-duration: 15s;
            border-color: rgba(255,255,255,0.04);
        }

        .geo-shape:nth-child(3) {
            width: 150px;
            height: 150px;
            border-radius: 50% 20% 50% 20%;
            top: 60%;
            left: 10%;
            animation-delay: -7s;
            animation-duration: 20s;
        }

        .geo-shape:nth-child(4) {
            width: 100px;
            height: 100px;
            border-radius: 20%;
            top: 20%;
            right: 15%;
            animation-delay: -5s;
            animation-duration: 12s;
            transform: rotate(45deg);
        }

        .geo-shape:nth-child(5) {
            width: 250px;
            height: 250px;
            border-radius: 63% 37% 54% 46% / 55% 48% 52% 45%;
            bottom: 5%;
            left: 30%;
            animation-delay: -10s;
            animation-duration: 22s;
            border-color: rgba(255,255,255,0.03);
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(20px, -30px) rotate(5deg); }
            50% { transform: translate(-10px, 20px) rotate(-3deg); }
            75% { transform: translate(15px, 10px) rotate(2deg); }
        }

        /* Grid pattern overlay */
        .grid-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 0;
            pointer-events: none;
        }

        .brand {
            text-align: center;
            margin-bottom: 32px;
            color: #fff;
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 16px;
            margin-bottom: 16px;
            box-shadow: 0 8px 32px rgba(59, 130, 246, 0.3);
        }

        .brand-logo svg {
            width: 28px;
            height: 28px;
            fill: white;
        }

        .brand h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.8) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand p {
            font-size: 0.85rem;
            opacity: 0.6;
            font-weight: 400;
            letter-spacing: 0.5px;
        }

        .container {
            background-color: #fff;
            border-radius: 24px;
            box-shadow: 
                0 25px 60px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255,255,255,0.05);
            position: relative;
            overflow: hidden;
            width: 880px;
            max-width: 100%;
            min-height: 560px;
            z-index: 1;
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
            color: #3b82f6;
            font-size: 13px;
            text-decoration: none;
            margin: 15px 0 10px;
            transition: color 0.3s;
            font-weight: 500;
        }

        .container a:hover {
            color: #8b5cf6;
            text-decoration: none;
        }

        .container button {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: #fff;
            font-size: 13px;
            padding: 13px 50px;
            border: 1px solid transparent;
            border-radius: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
            text-transform: none;
            margin-top: 10px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.35);
            position: relative;
            overflow: hidden;
        }

        .container button::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s ease;
        }

        .container button:hover::after {
            left: 100%;
        }

        .container button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.45);
        }

        .container button:active {
            transform: translateY(0);
        }

        .container button.hidden {
            background: transparent;
            border-color: rgba(255,255,255,0.5);
            box-shadow: none;
            backdrop-filter: blur(4px);
        }

        .container button.hidden:hover {
            background: rgba(255,255,255,0.15);
            border-color: #fff;
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
            color: #1e293b;
            margin-bottom: 10px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .container input {
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            margin: 6px 0;
            padding: 12px 16px;
            font-size: 13px;
            border-radius: 12px;
            width: 100%;
            outline: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            color: #1e293b;
        }

        .container input::placeholder {
            color: #94a3b8;
        }

        .container input:focus {
            border-color: #3b82f6;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08);
        }

        .container input.error {
            border-color: #ef4444;
            background-color: #fef2f2;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.06);
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
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: #fff;
            border-color: transparent;
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            height: 100%;
            color: #fff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: all 0.6s ease-in-out;
            overflow: hidden;
        }

        .toggle::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(ellipse at 30% 50%, rgba(59, 130, 246, 0.2) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 50%, rgba(139, 92, 246, 0.15) 0%, transparent 50%);
            z-index: 0;
        }

        .toggle::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
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

        .toggle-panel {
            z-index: 1;
        }

        .toggle-panel .icon-box {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.3) 0%, rgba(139, 92, 246, 0.3) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(8px);
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
            color: #3b82f6;
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
            border-radius: 14px;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .register-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s ease;
        }

        .register-btn:hover::after {
            left: 100%;
        }

        .register-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            text-decoration: none !important;
        }

        .register-btn i {
            font-size: 18px;
        }

        .student-btn {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .teacher-btn {
            background: linear-gradient(135deg, #10b981, #059669);
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
            color: #3b82f6 !important;
            font-size: 13px !important;
            padding: 5px 0 !important;
            margin-bottom: 10px !important;
            cursor: pointer;
            box-shadow: none !important;
            text-transform: none !important;
        }

        .back-btn:hover {
            color: #8b5cf6 !important;
            transform: none !important;
            box-shadow: none !important;
        }

        .select-input {
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            margin: 6px 0;
            padding: 12px 16px;
            font-size: 13px;
            border-radius: 12px;
            width: 100%;
            outline: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: 'Montserrat', sans-serif;
            color: #1e293b;
            cursor: pointer;
        }

        .select-input:focus {
            border-color: #3b82f6;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.08);
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
                color: #3b82f6;
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
    <!-- Decorative Background Elements -->
    <div class="geo-shapes">
        <div class="geo-shape"></div>
        <div class="geo-shape"></div>
        <div class="geo-shape"></div>
        <div class="geo-shape"></div>
        <div class="geo-shape"></div>
    </div>
    <div class="grid-pattern"></div>

    <div class="brand">
        <div class="brand-logo">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L3 7v10l9 5 9-5V7l-9-5zm0 2.18l6.2 3.45v2.3L12 13.36 5.8 9.93v-2.3L12 4.18zM5.8 11.64L12 15.05l6.2-3.41v4.73L12 19.82l-6.2-3.45v-4.73z"/>
            </svg>
        </div>
        <h1>ZAFProctor</h1>
        <p>Sistem Ujian Online dengan AI Proctoring</p>
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
