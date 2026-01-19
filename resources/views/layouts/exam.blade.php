<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ZAFProctor') }} - Ujian</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --exam-primary: #4f46e5;
            --exam-primary-dark: #4338ca;
            --exam-success: #10b981;
            --exam-warning: #f59e0b;
            --exam-danger: #ef4444;
            --exam-sidebar-bg: #1e293b;
            --exam-header-bg: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            --exam-body-bg: #f1f5f9;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        /* Prevent text selection */
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(79, 70, 229, 0.4);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(79, 70, 229, 0.6);
        }

        /* Timer pulse animation */
        @keyframes timerPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .timer-warning {
            animation: timerPulse 1s ease-in-out infinite;
        }

        /* Question card hover */
        .question-nav-btn {
            transition: all 0.2s ease;
        }
        .question-nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Option card styles */
        .option-card {
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }
        .option-card:hover {
            border-color: var(--exam-primary);
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.05) 0%, rgba(99, 102, 241, 0.05) 100%);
            transform: translateX(4px);
        }
        .option-card.selected {
            border-color: var(--exam-primary);
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(99, 102, 241, 0.1) 100%);
        }

        /* Glass effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, var(--exam-primary) 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Mono font for timer */
        .font-mono-timer {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Smooth animations */
        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Camera preview styling */
        .camera-container {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .camera-container::before {
            content: '';
            position: absolute;
            inset: 0;
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 12px;
            pointer-events: none;
            z-index: 1;
        }

        /* Progress indicator */
        .progress-ring {
            transform: rotate(-90deg);
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased no-select" style="background: var(--exam-body-bg);" oncontextmenu="return false;">
    <div id="exam-container" class="min-h-screen">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
