<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZAFProctor') }} - Reset Password</title>

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
            min-height: 500px;
            display: flex;
        }

        .form-section {
            width: 50%;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-section h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }

        .form-section p {
            font-size: 14px;
            line-height: 20px;
            letter-spacing: 0.3px;
            color: #666;
            margin-bottom: 25px;
        }

        .form-section input {
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

        .form-section input:focus {
            border-color: #667eea;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-section input.error {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .form-section input[readonly] {
            background-color: #e5e7eb;
            cursor: not-allowed;
        }

        .input-group {
            position: relative;
            width: 100%;
        }

        .input-group input {
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px;
            font-size: 14px;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: #667eea;
        }

        .form-section button[type="submit"] {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            font-size: 12px;
            padding: 14px 50px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            width: 100%;
        }

        .form-section button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .form-section a {
            color: #512da8;
            font-size: 13px;
            text-decoration: none;
            margin-top: 15px;
            display: inline-block;
            transition: color 0.3s;
        }

        .form-section a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        .alert-success {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
        }

        .info-section {
            width: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px;
            color: #fff;
            text-align: center;
        }

        .info-section .icon-box {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
        }

        .info-section .icon-box i {
            font-size: 36px;
        }

        .info-section h1 {
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .info-section p {
            font-size: 14px;
            line-height: 1.6;
            opacity: 0.9;
            margin-bottom: 25px;
        }

        .info-section a {
            background: transparent;
            border: 2px solid #fff;
            color: #fff;
            padding: 12px 40px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .info-section a:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column-reverse;
                min-height: auto;
            }

            .form-section,
            .info-section {
                width: 100%;
                padding: 40px 30px;
            }

            .info-section {
                border-radius: 30px 30px 0 0;
                padding: 30px;
            }

            .info-section .icon-box {
                width: 60px;
                height: 60px;
                margin-bottom: 15px;
            }

            .info-section .icon-box i {
                font-size: 28px;
            }

            .info-section h1 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <!-- Brand -->
    <div class="brand">
        <h1>📝 ZAFProctor</h1>
        <p>Sistem Ujian Online dengan Camera Proctoring</p>
    </div>

    <!-- Container -->
    <div class="container">
        <!-- Form Section -->
        <div class="form-section">
            <h1><i class="fa-solid fa-key"></i> Reset Password</h1>
            <p>Masukkan password baru Anda untuk mengakses akun kembali.</p>

            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update.reset') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email -->
                <input type="email" name="email" placeholder="Email" 
                       value="{{ $email ?? old('email') }}" readonly>

                <!-- Password -->
                <div class="input-group">
                    <input type="password" name="password" id="password" 
                           placeholder="Password Baru" required autofocus
                           class="{{ $errors->has('password') ? 'error' : '' }}">
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <!-- Confirm Password -->
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           placeholder="Konfirmasi Password Baru" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <button type="submit">
                    <i class="fa-solid fa-check"></i> Reset Password
                </button>

                <a href="{{ route('login') }}">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Login
                </a>
            </form>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="icon-box">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h1>Keamanan Akun</h1>
            <p>Pastikan password baru Anda kuat dan mudah diingat. Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol.</p>
            <a href="{{ route('login') }}">
                <i class="fa-solid fa-arrow-right"></i> Ke Halaman Login
            </a>
        </div>
    </div>

    <script>
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
    </script>
</body>
</html>
