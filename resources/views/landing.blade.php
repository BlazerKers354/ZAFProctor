<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZAFProctor - Sistem Ujian Online dengan Pengawasan Kamera</title>
    <!-- Bootstrap 5.3.2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Phosphor Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        /* ===== Base ===== */
        body { font-family: 'Figtree', sans-serif; scroll-behavior: smooth; background-color: #020617; color: #fff; }
        a { text-decoration: none; }

        /* ===== Gradient & Glass ===== */
        .gradient-text {
            background: linear-gradient(135deg, #818cf8, #c084fc, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .glass-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
        }

        /* ===== Hero Pattern & Blobs ===== */
        .hero-pattern {
            background-image: radial-gradient(circle at 20% 50%, rgba(99,102,241,0.15) 0%, transparent 50%),
                              radial-gradient(circle at 80% 20%, rgba(168,85,247,0.12) 0%, transparent 50%),
                              radial-gradient(circle at 40% 80%, rgba(236,72,153,0.08) 0%, transparent 50%);
        }
        .blob { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
        @keyframes morph {
            0%, 100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            25% { border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%; }
            50% { border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%; }
            75% { border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%; }
        }
        .morph { animation: morph 8s ease-in-out infinite; }

        /* ===== Animations ===== */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        @keyframes slide-up {
            0% { transform: translateY(30px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        @keyframes fade-in {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delay { animation: float 6s ease-in-out 2s infinite; }
        .animate-float-slow { animation: float 8s ease-in-out 1s infinite; }
        .animate-pulse-soft { animation: pulse-soft 3s ease-in-out infinite; }
        .animate-slide-up { animation: slide-up 0.6s ease-out; }
        .animate-fade-in { animation: fade-in 0.8s ease-out; }
        .animate-pulse { animation: pulse-soft 2s ease-in-out infinite; }

        /* ===== Component Styles ===== */
        .feature-card { transition: all 0.3s ease; }
        .feature-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(79,70,229,0.12); }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: scale(1.05); }

        /* ===== Navbar ===== */
        .landing-nav { position: fixed; top: 0; width: 100%; z-index: 1040; transition: all 0.3s ease; }
        .landing-nav.scrolled { background: rgba(2,6,23,0.8); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(30,41,59,0.5); }
        .nav-link-landing { color: #cbd5e1; font-size: 0.875rem; transition: color 0.2s; }
        .nav-link-landing:hover { color: #fff; }

        /* ===== Section Styles ===== */
        .section-badge {
            display: inline-flex; align-items: center; gap: 0.5rem;
            border-radius: 9999px; padding: 0.25rem 1rem; font-size: 0.875rem;
        }
        .section-py { padding-top: 6rem; padding-bottom: 6rem; }

        /* ===== Color Utilities ===== */
        .text-slate-300 { color: #cbd5e1 !important; }
        .text-slate-400 { color: #94a3b8 !important; }
        .text-slate-500 { color: #64748b !important; }
        .text-slate-600 { color: #475569 !important; }
        .text-slate-700 { color: #334155 !important; }
        .text-indigo-300 { color: #a5b4fc !important; }
        .text-indigo-400 { color: #818cf8 !important; }
        .text-purple-400 { color: #c084fc !important; }
        .text-emerald-400 { color: #34d399 !important; }
        .text-green-400 { color: #4ade80 !important; }
        .text-green-500 { color: #22c55e !important; }
        .text-rose-400 { color: #fb7185 !important; }
        .text-amber-400 { color: #fbbf24 !important; }
        .text-sky-400 { color: #38bdf8 !important; }
        .text-violet-400 { color: #a78bfa !important; }
        .text-blue-400 { color: #60a5fa !important; }
        .text-pink-300 { color: #f9a8d4 !important; }
        .text-pink-400 { color: #f472b6 !important; }
        .text-red-300 { color: #fca5a5 !important; }
        .text-blue-300 { color: #93c5fd !important; }
        .text-emerald-300 { color: #6ee7b7 !important; }
        .text-yellow-300 { color: #fde047 !important; }

        /* ===== Background Utilities ===== */
        .bg-slate-700 { background-color: #334155 !important; }
        .bg-slate-800 { background-color: #1e293b !important; }
        .bg-slate-900 { background-color: #0f172a !important; }
        .bg-slate-950 { background-color: #020617 !important; }
        .bg-indigo-500 { background-color: #6366f1 !important; }
        .bg-indigo-600 { background-color: #4f46e5 !important; }
        .bg-green-500 { background-color: #22c55e !important; }

        /* BG with opacity */
        .bg-slate-700-50 { background-color: rgba(51,65,85,0.5) !important; }
        .bg-slate-800-80 { background-color: rgba(30,41,59,0.8) !important; }
        .bg-slate-800-50 { background-color: rgba(30,41,59,0.5) !important; }
        .bg-slate-900-50 { background-color: rgba(15,23,42,0.5) !important; }
        .bg-slate-900-30 { background-color: rgba(15,23,42,0.3) !important; }
        .bg-indigo-500-10 { background-color: rgba(99,102,241,0.1) !important; }
        .bg-indigo-500-20 { background-color: rgba(99,102,241,0.2) !important; }
        .bg-indigo-600-5 { background-color: rgba(79,70,229,0.05) !important; }
        .bg-indigo-600-10 { background-color: rgba(79,70,229,0.1) !important; }
        .bg-indigo-600-20 { background-color: rgba(79,70,229,0.2) !important; }
        .bg-purple-500-10 { background-color: rgba(168,85,247,0.1) !important; }
        .bg-purple-500-20 { background-color: rgba(168,85,247,0.2) !important; }
        .bg-purple-600-5 { background-color: rgba(147,51,234,0.05) !important; }
        .bg-purple-600-20 { background-color: rgba(147,51,234,0.2) !important; }
        .bg-emerald-500-10 { background-color: rgba(16,185,129,0.1) !important; }
        .bg-emerald-500-20 { background-color: rgba(16,185,129,0.2) !important; }
        .bg-green-500-20 { background-color: rgba(34,197,94,0.2) !important; }
        .bg-amber-500-20 { background-color: rgba(245,158,11,0.2) !important; }
        .bg-amber-600-20 { background-color: rgba(217,119,6,0.2) !important; }
        .bg-rose-500-20 { background-color: rgba(244,63,94,0.2) !important; }
        .bg-rose-600-20 { background-color: rgba(225,29,72,0.2) !important; }
        .bg-sky-500-20 { background-color: rgba(14,165,233,0.2) !important; }
        .bg-sky-600-20 { background-color: rgba(2,132,199,0.2) !important; }
        .bg-violet-500-20 { background-color: rgba(139,92,246,0.2) !important; }
        .bg-blue-500-20 { background-color: rgba(59,130,246,0.2) !important; }
        .bg-blue-600-20 { background-color: rgba(37,99,235,0.2) !important; }
        .bg-red-500-20 { background-color: rgba(239,68,68,0.2) !important; }
        .bg-pink-500-10 { background-color: rgba(236,72,153,0.1) !important; }
        .bg-pink-600-20 { background-color: rgba(219,39,119,0.2) !important; }
        .bg-red-500-80 { background-color: rgba(239,68,68,0.8) !important; }
        .bg-yellow-500-80 { background-color: rgba(234,179,8,0.8) !important; }
        .bg-green-500-80 { background-color: rgba(34,197,94,0.8) !important; }
        .bg-white-5 { background-color: rgba(255,255,255,0.05) !important; }
        .bg-white-10 { background-color: rgba(255,255,255,0.1) !important; }
        .bg-white-20 { background-color: rgba(255,255,255,0.2) !important; }

        /* Gradient BGs */
        .bg-gradient-indigo { background: linear-gradient(to bottom right, rgba(99,102,241,0.2), rgba(79,70,229,0.2)); }
        .bg-gradient-emerald { background: linear-gradient(to bottom right, rgba(16,185,129,0.2), rgba(5,150,105,0.2)); }
        .bg-gradient-purple { background: linear-gradient(to bottom right, rgba(168,85,247,0.2), rgba(147,51,234,0.2)); }
        .bg-gradient-amber { background: linear-gradient(to bottom right, rgba(245,158,11,0.2), rgba(217,119,6,0.2)); }
        .bg-gradient-rose { background: linear-gradient(to bottom right, rgba(244,63,94,0.2), rgba(225,29,72,0.2)); }
        .bg-gradient-sky { background: linear-gradient(to bottom right, rgba(14,165,233,0.2), rgba(2,132,199,0.2)); }
        .bg-gradient-blue { background: linear-gradient(to bottom right, rgba(59,130,246,0.2), rgba(37,99,235,0.2)); }
        .bg-gradient-green { background: linear-gradient(to bottom right, rgba(16,185,129,0.2), rgba(34,197,94,0.2)); }
        .bg-gradient-violet { background: linear-gradient(to bottom right, rgba(139,92,246,0.2), rgba(168,85,247,0.2)); }
        .bg-gradient-step1 { background: linear-gradient(to bottom right, #6366f1, #4f46e5); }
        .bg-gradient-step2 { background: linear-gradient(to bottom right, #a855f7, #9333ea); }
        .bg-gradient-step3 { background: linear-gradient(to bottom right, #ec4899, #e11d48); }
        .bg-gradient-banner { background: linear-gradient(to bottom right, #4f46e5, #9333ea, #ec4899); }
        .bg-gradient-logo { background: linear-gradient(to bottom right, #3b82f6, #7c3aed); }
        .bg-gradient-logo-hero { background: linear-gradient(to bottom right, #3b82f6, #8b5cf6); }

        /* ===== Border Utilities ===== */
        .border-slate-700-50 { border-color: rgba(51,65,85,0.5) !important; }
        .border-slate-800-50 { border-color: rgba(30,41,59,0.5) !important; }
        .border-slate-600-50 { border-color: rgba(71,85,105,0.5) !important; }
        .border-indigo-500 { border-color: #6366f1 !important; }
        .border-indigo-500-20 { border-color: rgba(99,102,241,0.2) !important; }
        .border-indigo-500-30 { border-color: rgba(99,102,241,0.3) !important; }
        .border-purple-500-20 { border-color: rgba(168,85,247,0.2) !important; }
        .border-emerald-500-20 { border-color: rgba(16,185,129,0.2) !important; }
        .border-emerald-500-30 { border-color: rgba(16,185,129,0.3) !important; }
        .border-green-500-30 { border-color: rgba(34,197,94,0.3) !important; }
        .border-blue-500-20 { border-color: rgba(59,130,246,0.2) !important; }
        .border-violet-500-20 { border-color: rgba(139,92,246,0.2) !important; }
        .border-white-10 { border-color: rgba(255,255,255,0.1) !important; }
        .border-white-20 { border-color: rgba(255,255,255,0.2) !important; }
        .border-slate-600 { border-color: #475569 !important; }

        /* ===== Sizing ===== */
        .w-2 { width: 0.5rem; }
        .h-2 { height: 0.5rem; }
        .w-3 { width: 0.75rem; }
        .h-3 { height: 0.75rem; }
        .w-4 { width: 1rem; }
        .h-4 { height: 1rem; }
        .w-5 { width: 1.25rem; }
        .h-5 { height: 1.25rem; }
        .w-6 { width: 1.5rem; }
        .h-6 { height: 1.5rem; }
        .w-7 { width: 1.75rem; }
        .h-7 { height: 1.75rem; }
        .w-8 { width: 2rem; }
        .h-8 { height: 2rem; }
        .w-9 { width: 2.25rem; }
        .h-9 { height: 2.25rem; }
        .w-12 { width: 3rem; }
        .h-12 { height: 3rem; }
        .w-14 { width: 3.5rem; }
        .h-14 { height: 3.5rem; }
        .w-16 { width: 4rem; }
        .h-16 { height: 4rem; }

        /* ===== Spacing ===== */
        .gap-1 { gap: 0.25rem; }
        .gap-1-5 { gap: 0.375rem; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 0.75rem; }
        .gap-4 { gap: 1rem; }
        .gap-6 { gap: 1.5rem; }
        .gap-8 { gap: 2rem; }
        .gap-12 { gap: 3rem; }
        .mb-16 { margin-bottom: 4rem !important; }
        .py-16 { padding-top: 4rem !important; padding-bottom: 4rem !important; }
        .py-20 { padding-top: 5rem !important; padding-bottom: 5rem !important; }
        .mb-1 { margin-bottom: 0.25rem !important; }

        /* ===== Typography ===== */
        .text-xxs { font-size: 0.625rem; }
        .text-xs { font-size: 0.75rem; }
        .text-sm { font-size: 0.875rem; }
        .text-base { font-size: 1rem; }
        .text-lg { font-size: 1.125rem; }
        .text-xl { font-size: 1.25rem; }
        .text-2xl { font-size: 1.5rem; }
        .text-3xl { font-size: 1.875rem; }
        .text-4xl { font-size: 2.25rem; }
        .text-5xl { font-size: 3rem; }
        .text-6xl { font-size: 3.75rem; }
        .leading-tight { line-height: 1.25; }
        .leading-relaxed { line-height: 1.625; }
        .font-mono { font-family: monospace; }

        /* ===== Border Radius ===== */
        .rounded-xl { border-radius: 0.75rem !important; }
        .rounded-2xl { border-radius: 1rem !important; }
        .rounded-3xl { border-radius: 1.5rem !important; }
        .rounded-lg { border-radius: 0.5rem !important; }
        .rounded-full { border-radius: 9999px !important; }

        /* ===== Misc Utilities ===== */
        .blur-3xl { filter: blur(64px); }
        .backdrop-blur { backdrop-filter: blur(12px); }
        .opacity-3 { opacity: 0.03; }
        .shadow-indigo { box-shadow: 0 10px 30px rgba(79,70,229,0.25); }
        .shadow-indigo-lg { box-shadow: 0 10px 30px rgba(79,70,229,0.4); }
        .shadow-indigo-step { box-shadow: 0 10px 30px rgba(99,102,241,0.3); }
        .shadow-purple-step { box-shadow: 0 10px 30px rgba(168,85,247,0.3); }
        .shadow-pink-step { box-shadow: 0 10px 30px rgba(236,72,153,0.3); }
        .rotate-3 { transform: rotate(3deg); }
        .rotate-n3 { transform: rotate(-3deg); }
        .hover-rotate-0:hover { transform: rotate(0deg); }
        .translate-y-n4 { transform: translateY(-1rem); }

        /* ===== Buttons ===== */
        .btn-indigo {
            background-color: #4f46e5; color: #fff !important;
            box-shadow: 0 10px 30px rgba(79,70,229,0.25);
            transition: all 0.2s;
        }
        .btn-indigo:hover {
            background-color: #6366f1; color: #fff !important;
            box-shadow: 0 10px 30px rgba(99,102,241,0.4);
        }
        .btn-glass {
            background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
            color: #fff !important; transition: all 0.2s;
        }
        .btn-glass:hover {
            background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);
            color: #fff !important;
        }

        /* ===== Hover Effects ===== */
        .feature-card:hover .border-hover-indigo { border-color: rgba(99,102,241,0.3) !important; }
        .feature-card:hover .border-hover-emerald { border-color: rgba(16,185,129,0.3) !important; }
        .feature-card:hover .border-hover-purple { border-color: rgba(168,85,247,0.3) !important; }
        .feature-card:hover .border-hover-amber { border-color: rgba(245,158,11,0.3) !important; }
        .feature-card:hover .border-hover-rose { border-color: rgba(244,63,94,0.3) !important; }
        .feature-card:hover .border-hover-sky { border-color: rgba(14,165,233,0.3) !important; }

        .role-card { transition: all 0.3s ease; }
        .role-card:hover.hover-blue { border-color: rgba(59,130,246,0.3) !important; }
        .role-card:hover.hover-emerald { border-color: rgba(16,185,129,0.3) !important; }
        .role-card:hover.hover-violet { border-color: rgba(139,92,246,0.3) !important; }

        /* ===== Mockup ===== */
        .mockup-option {
            display: flex; align-items: center; padding: 0.625rem; border-radius: 0.5rem;
            border: 1px solid transparent; transition: all 0.2s; cursor: pointer;
        }
        .mockup-option:hover { background: rgba(51,65,85,0.5); border-color: rgba(71,85,105,0.5); }
        .mockup-option-selected { background: rgba(99,102,241,0.1); border-color: rgba(99,102,241,0.3); cursor: default; }
        .mockup-dot {
            width: 1.5rem; height: 1.5rem; border-radius: 50%;
            border: 2px solid #475569; margin-right: 0.75rem; flex-shrink: 0;
        }
        .mockup-dot-selected {
            border-color: #6366f1; background: #6366f1;
            display: flex; align-items: center; justify-content: center;
        }

        /* ===== Grid Pattern ===== */
        .grid-pattern { position: absolute; inset: 0; opacity: 0.03; }

        /* ===== Connection Line ===== */
        .connection-line {
            position: absolute; top: 4rem; left: 16.67%; right: 16.67%; height: 2px;
            background: linear-gradient(to right, rgba(99,102,241,0.5), rgba(168,85,247,0.5), rgba(236,72,153,0.5));
        }

        /* ===== Check list item ===== */
        .check-list li { display: flex; align-items: center; font-size: 0.875rem; color: #cbd5e1; }
        .check-list li svg { margin-right: 0.75rem; flex-shrink: 0; }

        /* ===== Responsive ===== */
        @media (min-width: 768px) {
            .text-md-4xl { font-size: 2.25rem !important; }
            .text-md-5xl { font-size: 3rem !important; }
        }
        @media (min-width: 992px) {
            .text-lg-6xl { font-size: 3.75rem !important; }
        }
        @media (max-width: 767.98px) {
            .hero-title { font-size: 2.25rem !important; }
        }
    </style>
</head>
<body class="antialiased">

    <!-- Navigation -->
    <nav class="landing-nav" id="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center" style="height: 4rem;">
                <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-white">
                    <div class="w-9 h-9 bg-gradient-logo-hero rounded-xl d-flex align-items-center justify-content-center shadow-indigo">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="white" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L3 7v10l9 5 9-5V7l-9-5zm0 2.18l6.2 3.45v2.3L12 13.36 5.8 9.93v-2.3L12 4.18zM5.8 11.64L12 15.05l6.2-3.41v4.73L12 19.82l-6.2-3.45v-4.73z"/>
                        </svg>
                    </div>
                    <span class="text-xl fw-bold text-white">ZAF<span class="text-indigo-400">Proctor</span></span>
                </a>
                <div class="d-none d-md-flex align-items-center gap-4">
                    <a href="#features" class="nav-link-landing">Fitur</a>
                    <a href="#how-it-works" class="nav-link-landing">Cara Kerja</a>
                    <a href="#roles" class="nav-link-landing">Peran</a>
                    <a href="#stats" class="nav-link-landing">Statistik</a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('login') }}" class="text-sm text-slate-300 fw-medium px-3 py-2" style="transition:color .2s;">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-indigo rounded-xl fw-semibold px-3 py-2 text-sm">
                        Daftar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="position-relative d-flex align-items-center hero-pattern overflow-hidden" style="min-height:100vh; padding-top:4rem;">
        <!-- Animated blobs -->
        <div class="position-absolute blur-3xl animate-float morph bg-indigo-600-20 rounded-circle" style="top:5rem;left:2.5rem;width:18rem;height:18rem;"></div>
        <div class="position-absolute blur-3xl animate-float-delay morph bg-purple-600-5 rounded-circle" style="bottom:5rem;right:2.5rem;width:24rem;height:24rem;opacity:0.15;"></div>
        <div class="position-absolute blur-3xl animate-float-slow rounded-circle" style="top:50%;left:50%;transform:translate(-50%,-50%);width:37.5rem;height:37.5rem;background:rgba(99,102,241,0.05);"></div>

        <!-- Grid pattern -->
        <div class="grid-pattern">
            <svg width="100%" height="100%">
                <defs>
                    <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse">
                        <path d="M 60 0 L 0 0 0 60" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>

        <div class="container position-relative py-5 w-100">
            <div class="row align-items-center" style="row-gap:3rem;">
                <!-- Left Content -->
                <div class="col-lg-6 animate-slide-up">
                    <div class="section-badge bg-indigo-500-10 border border-indigo-500-20 mb-4">
                        <span class="w-2 h-2 bg-green-500 rounded-circle d-inline-block animate-pulse"></span>
                        <span class="text-sm text-indigo-300">Platform CBT Terpercaya</span>
                    </div>

                    <h1 class="hero-title text-4xl text-md-5xl text-lg-6xl fw-bolder leading-tight mb-4">
                        Ujian Online<br>
                        <span class="gradient-text">Aman &amp; Terpercaya</span><br>
                        dengan AI Proctoring
                    </h1>

                    <p class="text-lg text-slate-400 leading-relaxed mb-4" style="max-width:36rem;">
                        Platform Computer Based Test (CBT) dengan pengawasan kamera real-time,
                        deteksi kecurangan otomatis, dan penilaian instan untuk menjamin
                        integritas setiap ujian online.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="{{ route('register') }}"
                           class="btn btn-indigo rounded-xl fw-semibold px-4 py-3 d-inline-flex align-items-center gap-2">
                            Mulai Sekarang
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <a href="{{ route('guide.download') }}"
                           class="btn btn-glass rounded-xl fw-semibold px-4 py-3 d-inline-flex align-items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Panduan Pengguna
                            <span class="text-xs bg-indigo-500-20 text-indigo-300 px-2 rounded-full" style="padding-top:2px;padding-bottom:2px;">PDF</span>
                        </a>
                    </div>

                    <!-- Trust badges -->
                    <div class="d-flex align-items-center gap-4 text-sm text-slate-500">
                        <div class="d-flex align-items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Gratis</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Tanpa Setup Ribet</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Aman</span>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Mockup -->
                <div class="col-lg-6 animate-fade-in d-none d-lg-block">
                    <div class="position-relative">
                        <!-- Main mockup card -->
                        <div class="glass-card rounded-2xl p-1 shadow-lg">
                            <div class="bg-slate-900 rounded-xl overflow-hidden">
                                <!-- Browser bar -->
                                <div class="d-flex align-items-center gap-2 px-3 py-2 bg-slate-800-80 border-bottom border-slate-700-50">
                                    <div class="d-flex gap-1-5">
                                        <div class="w-3 h-3 rounded-circle bg-red-500-80"></div>
                                        <div class="w-3 h-3 rounded-circle bg-yellow-500-80"></div>
                                        <div class="w-3 h-3 rounded-circle bg-green-500-80"></div>
                                    </div>
                                    <div class="flex-grow-1 mx-3">
                                        <div class="bg-slate-700-50 rounded-lg px-3 py-1 text-xs text-slate-400 text-center">
                                            zafproctor.app/ujian
                                        </div>
                                    </div>
                                </div>

                                <!-- Exam interface mockup -->
                                <div class="p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <div class="text-sm fw-bold text-white">Ujian Matematika Dasar</div>
                                            <div class="text-xs text-slate-500">Soal 5 dari 20</div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-green-500-20 border border-green-500-30 d-flex align-items-center justify-content-center">
                                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div class="bg-indigo-500-20 border border-indigo-500-30 px-3 py-1 rounded-lg">
                                                <span class="text-xs text-indigo-300 font-mono">01:23:45</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Question -->
                                    <div class="bg-slate-800-50 rounded-xl p-3 mb-3 border border-slate-700-50" style="border-width:1px;">
                                        <p class="text-sm text-slate-300 mb-3">Jika f(x) = 2x + 3 dan g(x) = x&#xB2; - 1, maka (f &#x2218; g)(2) = ...</p>
                                        <div class="d-flex flex-column gap-2">
                                            <div class="mockup-option" style="background:rgba(51,65,85,0.3);">
                                                <div class="mockup-dot"></div>
                                                <span class="text-xs text-slate-400">A. 5</span>
                                            </div>
                                            <div class="mockup-option mockup-option-selected">
                                                <div class="mockup-dot mockup-dot-selected">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span class="text-xs text-indigo-300 fw-medium">B. 9</span>
                                            </div>
                                            <div class="mockup-option" style="background:rgba(51,65,85,0.3);">
                                                <div class="mockup-dot"></div>
                                                <span class="text-xs text-slate-400">C. 7</span>
                                            </div>
                                            <div class="mockup-option" style="background:rgba(51,65,85,0.3);">
                                                <div class="mockup-dot"></div>
                                                <span class="text-xs text-slate-400">D. 11</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Navigation dots -->
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex gap-1">
                                            <div class="w-6 h-6 rounded bg-indigo-500 d-flex align-items-center justify-content-center text-white fw-bold" style="font-size:9px;">1</div>
                                            <div class="w-6 h-6 rounded bg-indigo-500 d-flex align-items-center justify-content-center text-white fw-bold" style="font-size:9px;">2</div>
                                            <div class="w-6 h-6 rounded bg-indigo-500 d-flex align-items-center justify-content-center text-white fw-bold" style="font-size:9px;">3</div>
                                            <div class="w-6 h-6 rounded bg-indigo-500 d-flex align-items-center justify-content-center text-white fw-bold" style="font-size:9px;">4</div>
                                            <div class="w-6 h-6 rounded d-flex align-items-center justify-content-center text-white fw-bold" style="font-size:9px;background:#818cf8;box-shadow:0 0 0 2px rgba(129,140,248,0.5);">5</div>
                                            <div class="w-6 h-6 rounded bg-slate-700 d-flex align-items-center justify-content-center text-slate-500" style="font-size:9px;">6</div>
                                            <div class="w-6 h-6 rounded bg-slate-700 d-flex align-items-center justify-content-center text-slate-500" style="font-size:9px;">7</div>
                                            <div class="text-xs text-slate-600 d-flex align-items-center ms-1">...</div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <div class="px-3 bg-slate-700 rounded-lg text-xs text-slate-400" style="padding-top:6px;padding-bottom:6px;">Sebelumnya</div>
                                            <div class="px-3 bg-indigo-600 rounded-lg text-xs text-white" style="padding-top:6px;padding-bottom:6px;">Selanjutnya</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating cards -->
                        <div class="position-absolute glass-card rounded-xl px-3 py-2 animate-float" style="top:-1rem;right:-1rem;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="w-8 h-8 bg-green-500-20 rounded-lg d-flex align-items-center justify-content-center">
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs fw-semibold text-white">Proctoring Aktif</div>
                                    <div class="text-xxs text-green-400">Kamera terhubung</div>
                                </div>
                            </div>
                        </div>

                        <div class="position-absolute glass-card rounded-xl px-3 py-2 animate-float-delay" style="bottom:-1.5rem;left:-1.5rem;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="w-8 h-8 bg-indigo-500-20 rounded-lg d-flex align-items-center justify-content-center">
                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs fw-semibold text-white">Auto-Grade</div>
                                    <div class="text-xxs text-indigo-400">Nilai otomatis</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="position-relative py-16 border-top border-bottom border-slate-800-50">
        <div class="container">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="stat-card text-center p-4 rounded-2xl bg-slate-900-50 border border-slate-800-50">
                        <div class="text-3xl text-md-4xl fw-bolder gradient-text mb-1">100%</div>
                        <div class="text-sm text-slate-500">Gratis Digunakan</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card text-center p-4 rounded-2xl bg-slate-900-50 border border-slate-800-50">
                        <div class="text-3xl text-md-4xl fw-bolder text-indigo-400 mb-1">24/7</div>
                        <div class="text-sm text-slate-500">Akses Kapan Saja</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card text-center p-4 rounded-2xl bg-slate-900-50 border border-slate-800-50">
                        <div class="text-3xl text-md-4xl fw-bolder text-purple-400 mb-1">Real-time</div>
                        <div class="text-sm text-slate-500">Monitoring Langsung</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card text-center p-4 rounded-2xl bg-slate-900-50 border border-slate-800-50">
                        <div class="text-3xl text-md-4xl fw-bolder text-emerald-400 mb-1">Auto</div>
                        <div class="text-sm text-slate-500">Penilaian Otomatis</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="position-relative section-py overflow-hidden">
        <div class="position-absolute blur-3xl bg-indigo-600-5 rounded-circle" style="top:0;right:0;width:24rem;height:24rem;"></div>
        <div class="position-absolute blur-3xl bg-purple-600-5 rounded-circle" style="bottom:0;left:0;width:24rem;height:24rem;"></div>

        <div class="container position-relative">
            <div class="text-center mb-16">
                <div class="section-badge bg-indigo-500-10 border border-indigo-500-20 mb-3">
                    <span class="text-sm text-indigo-400">Fitur Unggulan</span>
                </div>
                <h2 class="text-3xl text-md-4xl fw-bolder text-white mb-3">
                    Semua yang Anda Butuhkan untuk<br>
                    <span class="gradient-text">Ujian Online yang Aman</span>
                </h2>
                <p class="text-slate-400 mx-auto" style="max-width:42rem;">
                    Dirancang khusus untuk menjaga keamanan, integritas, dan kemudahan pelaksanaan ujian online
                </p>
            </div>

            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card bg-slate-900-50 border border-slate-800-50 rounded-2xl p-4 h-100" style="transition:all .3s;border-width:1px;">
                        <div class="w-12 h-12 bg-gradient-indigo rounded-xl d-flex align-items-center justify-content-center mb-3">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg fw-bold text-white mb-2">Pengawasan Webcam</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-0">
                            Snapshot otomatis webcam selama ujian untuk verifikasi identitas peserta secara berkala.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card bg-slate-900-50 border border-slate-800-50 rounded-2xl p-4 h-100" style="transition:all .3s;border-width:1px;">
                        <div class="w-12 h-12 bg-gradient-emerald rounded-xl d-flex align-items-center justify-content-center mb-3">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg fw-bold text-white mb-2">Deteksi Kecurangan</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-0">
                            Mendeteksi pergantian tab, keluar fullscreen, copy-paste, dan aktivitas mencurigakan lainnya.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card bg-slate-900-50 border border-slate-800-50 rounded-2xl p-4 h-100" style="transition:all .3s;border-width:1px;">
                        <div class="w-12 h-12 bg-gradient-purple rounded-xl d-flex align-items-center justify-content-center mb-3">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg fw-bold text-white mb-2">Timer Otomatis</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-0">
                            Durasi terkontrol dengan auto-submit saat waktu habis atau batas pelanggaran terlampaui.
                        </p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card bg-slate-900-50 border border-slate-800-50 rounded-2xl p-4 h-100" style="transition:all .3s;border-width:1px;">
                        <div class="w-12 h-12 bg-gradient-amber rounded-xl d-flex align-items-center justify-content-center mb-3">
                            <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-lg fw-bold text-white mb-2">Multi Tipe Soal</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-0">
                            Mendukung pilihan ganda dan essay dengan penilaian otomatis maupun manual oleh guru.
                        </p>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card bg-slate-900-50 border border-slate-800-50 rounded-2xl p-4 h-100" style="transition:all .3s;border-width:1px;">
                        <div class="w-12 h-12 bg-gradient-rose rounded-xl d-flex align-items-center justify-content-center mb-3">
                            <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg fw-bold text-white mb-2">Monitoring Real-time</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-0">
                            Guru dapat memantau semua peserta ujian secara langsung dalam tampilan grid interaktif.
                        </p>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card bg-slate-900-50 border border-slate-800-50 rounded-2xl p-4 h-100" style="transition:all .3s;border-width:1px;">
                        <div class="w-12 h-12 bg-gradient-sky rounded-xl d-flex align-items-center justify-content-center mb-3">
                            <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg fw-bold text-white mb-2">Laporan &amp; Statistik</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-0">
                            Dashboard lengkap dengan statistik ujian, hasil peserta, log pelanggaran, dan export data.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section id="how-it-works" class="position-relative section-py bg-slate-900-30">
        <div class="container">
            <div class="text-center mb-16">
                <div class="section-badge bg-purple-500-10 border border-purple-500-20 mb-3">
                    <span class="text-sm text-purple-400">Langkah Mudah</span>
                </div>
                <h2 class="text-3xl text-md-4xl fw-bolder text-white mb-3">
                    Bagaimana <span class="gradient-text">Cara Kerjanya?</span>
                </h2>
                <p class="text-slate-400 mx-auto" style="max-width:42rem;">
                    Tiga langkah sederhana dari pembuatan ujian hingga hasil otomatis
                </p>
            </div>

            <div class="row g-4 g-md-5 position-relative">
                <!-- Connection line -->
                <div class="connection-line d-none d-md-block"></div>

                <!-- Step 1 -->
                <div class="col-md-4 text-center position-relative">
                    <div class="d-inline-flex mb-4">
                        <div class="w-16 h-16 bg-gradient-step1 rounded-2xl d-flex align-items-center justify-content-center text-white fs-4 fw-bolder shadow-indigo-step rotate-3 hover-rotate-0" style="transition:transform .3s;">
                            1
                        </div>
                    </div>
                    <h3 class="text-lg fw-bold text-white mb-2">Guru Membuat Ujian</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Buat ujian, tambahkan soal pilihan ganda atau essay, dan atur pengaturan proctoring sesuai kebutuhan.
                    </p>
                    <div class="mt-3 d-inline-flex align-items-center gap-2 bg-indigo-500-10 rounded-xl px-3 py-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="text-xs text-indigo-300">Buat &amp; Konfigurasi</span>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-md-4 text-center position-relative">
                    <div class="d-inline-flex mb-4">
                        <div class="w-16 h-16 bg-gradient-step2 rounded-2xl d-flex align-items-center justify-content-center text-white fs-4 fw-bolder shadow-purple-step rotate-n3 hover-rotate-0" style="transition:transform .3s;">
                            2
                        </div>
                    </div>
                    <h3 class="text-lg fw-bold text-white mb-2">Siswa Mengerjakan</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Siswa masuk, verifikasi kamera, dan mengerjakan ujian dengan pengawasan otomatis yang berjalan di background.
                    </p>
                    <div class="mt-3 d-inline-flex align-items-center gap-2 bg-purple-500-10 rounded-xl px-3 py-2">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs text-purple-400">Proctoring Aktif</span>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-md-4 text-center position-relative">
                    <div class="d-inline-flex mb-4">
                        <div class="w-16 h-16 bg-gradient-step3 rounded-2xl d-flex align-items-center justify-content-center text-white fs-4 fw-bolder shadow-pink-step rotate-3 hover-rotate-0" style="transition:transform .3s;">
                            3
                        </div>
                    </div>
                    <h3 class="text-lg fw-bold text-white mb-2">Hasil Otomatis</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Nilai pilihan ganda otomatis, review essay oleh guru, dan laporan lengkap siap diunduh.
                    </p>
                    <div class="mt-3 d-inline-flex align-items-center gap-2 bg-pink-500-10 rounded-xl px-3 py-2">
                        <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-xs text-pink-300">Laporan &amp; Export</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Roles Section -->
    <section id="roles" class="position-relative section-py">
        <div class="container">
            <div class="text-center mb-16">
                <div class="section-badge bg-emerald-500-10 border border-emerald-500-20 mb-3">
                    <span class="text-sm text-emerald-400">Tiga Peran Utama</span>
                </div>
                <h2 class="text-3xl text-md-4xl fw-bolder text-white mb-3">
                    Dibuat untuk <span class="gradient-text">Semua Pengguna</span>
                </h2>
                <p class="text-slate-400 mx-auto" style="max-width:42rem;">
                    Setiap peran memiliki dashboard dan fitur yang dirancang khusus
                </p>
            </div>

            <div class="row g-4">
                <!-- Admin -->
                <div class="col-md-4">
                    <div class="role-card hover-blue position-relative bg-slate-900-50 border border-slate-800-50 rounded-2xl p-4 h-100" style="transition:all .3s;border-width:1px;">
                        <div class="position-absolute top-0 end-0 px-3 py-1 bg-blue-500-20 rounded-2xl" style="border-bottom-left-radius:0.75rem;border-top-right-radius:1rem;border-top-left-radius:0;border-bottom-right-radius:0;border-left:1px solid rgba(59,130,246,0.2);border-bottom:1px solid rgba(59,130,246,0.2);">
                            <span class="text-xs text-blue-400 fw-semibold">ADMIN</span>
                        </div>
                        <div class="w-14 h-14 bg-gradient-blue rounded-2xl d-flex align-items-center justify-content-center mb-4">
                            <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl fw-bold text-white mb-2">Administrator</h3>
                        <p class="text-slate-400 text-sm mb-4 leading-relaxed">Kelola seluruh sistem termasuk pengguna, kelas, dan mata pelajaran.</p>
                        <ul class="list-unstyled check-list d-flex flex-column gap-2">
                            <li>
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Manajemen pengguna &amp; approval
                            </li>
                            <li>
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Kelola kelas &amp; mata pelajaran
                            </li>
                            <li>
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Assign guru &amp; siswa
                            </li>
                            <li>
                                <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Dashboard statistik lengkap
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Teacher -->
                <div class="col-md-4">
                    <div class="role-card hover-emerald position-relative bg-slate-900-50 border border-slate-800-50 rounded-2xl p-4 h-100 translate-y-n4" style="transition:all .3s;border-width:1px;">
                        <div class="position-absolute top-0 end-0 px-3 py-1 bg-emerald-500-10 rounded-2xl" style="border-bottom-left-radius:0.75rem;border-top-right-radius:1rem;border-top-left-radius:0;border-bottom-right-radius:0;border-left:1px solid rgba(16,185,129,0.2);border-bottom:1px solid rgba(16,185,129,0.2);">
                            <span class="text-xs text-emerald-400 fw-semibold">GURU</span>
                        </div>
                        <div class="w-14 h-14 bg-gradient-green rounded-2xl d-flex align-items-center justify-content-center mb-4">
                            <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3 class="text-xl fw-bold text-white mb-2">Guru</h3>
                        <p class="text-slate-400 text-sm mb-4 leading-relaxed">Buat ujian, kelola soal, pantau peserta, dan lakukan penilaian.</p>
                        <ul class="list-unstyled check-list d-flex flex-column gap-2">
                            <li>
                                <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Buat &amp; kelola ujian
                            </li>
                            <li>
                                <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Bank soal PG &amp; Essay
                            </li>
                            <li>
                                <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Monitoring real-time
                            </li>
                            <li>
                                <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Grading &amp; export hasil
                            </li>
                            <li>
                                <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Import &amp; duplikasi soal
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Student -->
                <div class="col-md-4">
                    <div class="role-card hover-violet position-relative bg-slate-900-50 border border-slate-800-50 rounded-2xl p-4 h-100" style="transition:all .3s;border-width:1px;">
                        <div class="position-absolute top-0 end-0 px-3 py-1 bg-violet-500-20 rounded-2xl" style="border-bottom-left-radius:0.75rem;border-top-right-radius:1rem;border-top-left-radius:0;border-bottom-right-radius:0;border-left:1px solid rgba(139,92,246,0.2);border-bottom:1px solid rgba(139,92,246,0.2);">
                            <span class="text-xs text-violet-400 fw-semibold">SISWA</span>
                        </div>
                        <div class="w-14 h-14 bg-gradient-violet rounded-2xl d-flex align-items-center justify-content-center mb-4">
                            <svg class="w-7 h-7 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl fw-bold text-white mb-2">Siswa</h3>
                        <p class="text-slate-400 text-sm mb-4 leading-relaxed">Ikuti ujian online dengan mudah dan lihat hasil secara langsung.</p>
                        <ul class="list-unstyled check-list d-flex flex-column gap-2">
                            <li>
                                <svg class="w-4 h-4 text-violet-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Lihat daftar ujian tersedia
                            </li>
                            <li>
                                <svg class="w-4 h-4 text-violet-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Pre-check kamera otomatis
                            </li>
                            <li>
                                <svg class="w-4 h-4 text-violet-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Navigasi soal interaktif
                            </li>
                            <li>
                                <svg class="w-4 h-4 text-violet-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Lihat nilai &amp; review jawaban
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Download Guide Banner -->
    <section class="position-relative py-20">
        <div class="container" style="max-width:64rem;">
            <div class="position-relative overflow-hidden bg-gradient-banner rounded-3xl p-5" style="padding:2.5rem 3.5rem;">
                <!-- Background decoration -->
                <div class="position-absolute rounded-circle" style="top:0;right:0;width:16rem;height:16rem;background:rgba(255,255,255,0.05);transform:translate(25%,-50%);"></div>
                <div class="position-absolute rounded-circle" style="bottom:0;left:0;width:12rem;height:12rem;background:rgba(255,255,255,0.05);transform:translate(-25%,33%);"></div>

                <div class="row align-items-center position-relative g-4">
                    <div class="col-md-6">
                        <div class="section-badge bg-white-10 mb-3">
                            <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-sm fw-medium" style="color:rgba(255,255,255,0.9);">Panduan Lengkap</span>
                        </div>
                        <h3 class="text-2xl text-md-4xl fw-bolder text-white mb-3">
                            Download Panduan Pengguna
                        </h3>
                        <p class="leading-relaxed mb-4" style="color:rgba(255,255,255,0.7);">
                            Panduan PDF lengkap untuk Admin, Guru, dan Siswa.
                            Berisi langkah-langkah penggunaan, tips, screenshot, dan FAQ.
                        </p>
                        <a href="{{ route('guide.download') }}"
                           class="d-inline-flex align-items-center bg-white px-4 py-3 rounded-xl fw-bold shadow-lg gap-2" style="color:#4338ca;transition:all .2s;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download PDF Gratis
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </a>
                    </div>
                    <div class="col-md-6 d-none d-md-block">
                        <div class="bg-white-10 backdrop-blur rounded-2xl p-4 border border-white-10">
                            <div class="text-center mb-3">
                                <div class="d-inline-flex align-items-center justify-content-center w-14 h-14 bg-white-20 rounded-xl mb-2">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div class="text-sm fw-bold text-white">Isi Panduan</div>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-center gap-3 bg-white-5 rounded-xl px-3 py-2">
                                    <div class="w-8 h-8 bg-red-500-20 rounded-lg d-flex align-items-center justify-content-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs fw-semibold text-white">Panduan Admin</div>
                                        <div class="text-xxs" style="color:rgba(255,255,255,0.6);">Kelola user, kelas, mapel</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3 bg-white-5 rounded-xl px-3 py-2">
                                    <div class="w-8 h-8 bg-blue-500-20 rounded-lg d-flex align-items-center justify-content-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs fw-semibold text-white">Panduan Guru</div>
                                        <div class="text-xxs" style="color:rgba(255,255,255,0.6);">Buat ujian, soal, monitoring</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3 bg-white-5 rounded-xl px-3 py-2">
                                    <div class="w-8 h-8 bg-emerald-500-20 rounded-lg d-flex align-items-center justify-content-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs fw-semibold text-white">Panduan Siswa</div>
                                        <div class="text-xxs" style="color:rgba(255,255,255,0.6);">Ikuti ujian, kamera, hasil</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="position-relative section-py overflow-hidden">
        <div class="position-absolute hero-pattern" style="inset:0;"></div>
        <div class="position-absolute rounded-circle blur-3xl" style="top:50%;left:50%;transform:translate(-50%,-50%);width:31rem;height:31rem;background:rgba(79,70,229,0.1);"></div>

        <div class="container position-relative text-center" style="max-width:56rem;">
            <h2 class="text-3xl text-md-5xl fw-bolder text-white mb-4">
                Siap Memulai Ujian Online<br>
                <span class="gradient-text">yang Aman &amp; Terpercaya?</span>
            </h2>
            <p class="text-lg text-slate-400 mb-5 mx-auto" style="max-width:42rem;">
                Daftar sekarang dan nikmati semua fitur ZAFProctor secara gratis.
                Tidak perlu kartu kredit, langsung bisa digunakan.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="{{ route('register') }}"
                   class="btn btn-indigo rounded-xl fw-bold text-lg px-5 py-3 d-inline-flex align-items-center gap-2">
                    Daftar Gratis Sekarang
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="{{ route('guide.download') }}"
                   class="btn btn-glass rounded-xl fw-bold text-lg px-4 py-3 d-inline-flex align-items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Panduan PDF
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-top border-slate-800-50 py-5">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-gradient-logo rounded-lg d-flex align-items-center justify-content-center">
                            <span class="text-white fw-bold text-xs">Z</span>
                        </div>
                        <span class="text-lg fw-bold text-white">ZAF<span class="text-indigo-400">Proctor</span></span>
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed mb-0">
                        Sistem ujian online dengan pengawasan kamera untuk menjamin integritas ujian.
                    </p>
                </div>
                <div class="col-md-4">
                    <h4 class="text-sm fw-semibold text-slate-300 mb-3">Platform</h4>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="#features" class="text-sm text-slate-500" style="transition:color .2s;">Fitur</a></li>
                        <li><a href="#how-it-works" class="text-sm text-slate-500" style="transition:color .2s;">Cara Kerja</a></li>
                        <li><a href="#roles" class="text-sm text-slate-500" style="transition:color .2s;">Peran Pengguna</a></li>
                        <li><a href="{{ route('guide.download') }}" class="text-sm text-slate-500" style="transition:color .2s;">Panduan Pengguna (PDF)</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4 class="text-sm fw-semibold text-slate-300 mb-3">Akses</h4>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="{{ route('login') }}" class="text-sm text-slate-500" style="transition:color .2s;">Login</a></li>
                        <li><a href="{{ route('register') }}" class="text-sm text-slate-500" style="transition:color .2s;">Daftar</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-top border-slate-800-50 pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="text-sm text-slate-600 mb-2 mb-md-0">
                    &copy; {{ date('Y') }} ZAFProctor. All rights reserved.
                </p>
                <p class="text-sm text-slate-700 mb-0">
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <!-- Navbar scroll effect -->
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Footer link hover
        document.querySelectorAll('footer a').forEach(link => {
            link.addEventListener('mouseenter', () => link.style.color = '#cbd5e1');
            link.addEventListener('mouseleave', () => link.style.color = '');
        });
    </script>
</body>
</html>
