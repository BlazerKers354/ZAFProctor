<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZAFProctor') }} - @yield('title', 'Login')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Hide browser's default password reveal button */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear,
        input[type="password"]::-webkit-credentials-auto-fill-button,
        input[type="password"]::-webkit-clear-button {
            display: none !important;
        }
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8" style="background: #0f172a; position: relative; overflow: hidden;">
    <!-- Background Effects -->
    <div style="position: fixed; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(ellipse at 20% 50%, rgba(120, 119, 198, 0.18) 0%, transparent 50%), radial-gradient(ellipse at 80% 20%, rgba(59, 130, 246, 0.12) 0%, transparent 50%), radial-gradient(ellipse at 40% 80%, rgba(139, 92, 246, 0.1) 0%, transparent 50%); z-index: 0;"></div>
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-image: linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px); background-size: 60px 60px; z-index: 0;"></div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md" style="position: relative; z-index: 1;">
        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); border-radius: 14px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(59, 130, 246, 0.3);">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L3 7v10l9 5 9-5V7l-9-5zm0 2.18l6.2 3.45v2.3L12 13.36 5.8 9.93v-2.3L12 4.18zM5.8 11.64L12 15.05l6.2-3.41v4.73L12 19.82l-6.2-3.45v-4.73z"/>
                </svg>
            </div>
        </div>
        <h1 class="text-center text-3xl font-bold" style="background: linear-gradient(135deg, #fff, rgba(255,255,255,0.8)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -0.3px;">ZAFProctor</h1>
        <p class="mt-2 text-center text-sm" style="color: rgba(255,255,255,0.5);">
            Sistem Ujian Online dengan AI Proctoring
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md" style="position: relative; z-index: 1;">
        <div class="bg-white py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 relative overflow-hidden" style="border: 1px solid rgba(0,0,0,0.04);">
            @yield('content')
        </div>
    </div>
</body>
</html>
