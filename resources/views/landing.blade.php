<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZAFProctor - Sistem Ujian Online dengan Pengawasan Kamera</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Figtree', 'sans-serif'] },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delay': 'float 6s ease-in-out 2s infinite',
                        'float-slow': 'float 8s ease-in-out 1s infinite',
                        'pulse-soft': 'pulse-soft 3s ease-in-out infinite',
                        'slide-up': 'slide-up 0.6s ease-out',
                        'fade-in': 'fade-in 0.8s ease-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        'pulse-soft': {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.7' },
                        },
                        'slide-up': {
                            '0%': { transform: 'translateY(30px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        'fade-in': {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Figtree', sans-serif; scroll-behavior: smooth; }
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
        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(79,70,229,0.12);
        }
        .hero-pattern {
            background-image: radial-gradient(circle at 20% 50%, rgba(99,102,241,0.15) 0%, transparent 50%),
                              radial-gradient(circle at 80% 20%, rgba(168,85,247,0.12) 0%, transparent 50%),
                              radial-gradient(circle at 40% 80%, rgba(236,72,153,0.08) 0%, transparent 50%);
        }
        .blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        }
        @keyframes morph {
            0%, 100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            25% { border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%; }
            50% { border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%; }
            75% { border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%; }
        }
        .morph { animation: morph 8s ease-in-out infinite; }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: scale(1.05); }
    </style>
</head>
<body class="antialiased bg-slate-950 text-white">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/25">
                        <span class="text-white font-extrabold text-sm">Z</span>
                    </div>
                    <span class="text-xl font-bold text-white">ZAF<span class="text-indigo-400">Proctor</span></span>
                </a>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm text-slate-300 hover:text-white transition">Fitur</a>
                    <a href="#how-it-works" class="text-sm text-slate-300 hover:text-white transition">Cara Kerja</a>
                    <a href="#roles" class="text-sm text-slate-300 hover:text-white transition">Peran</a>
                    <a href="#stats" class="text-sm text-slate-300 hover:text-white transition">Statistik</a>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('login') }}" class="text-sm text-slate-300 hover:text-white font-medium transition px-4 py-2">Masuk</a>
                    <a href="{{ route('register') }}" class="text-sm bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2 rounded-xl font-semibold transition shadow-lg shadow-indigo-600/25">
                        Daftar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center hero-pattern overflow-hidden pt-16">
        <!-- Animated blobs -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-indigo-600/20 rounded-full blur-3xl animate-float morph"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-purple-600/15 rounded-full blur-3xl animate-float-delay morph"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-indigo-500/5 rounded-full blur-3xl animate-float-slow"></div>
        
        <!-- Grid pattern -->
        <div class="absolute inset-0 opacity-[0.03]">
            <svg width="100%" height="100%">
                <defs>
                    <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse">
                        <path d="M 60 0 L 0 0 0 60" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="animate-slide-up">
                    <div class="inline-flex items-center space-x-2 bg-indigo-500/10 border border-indigo-500/20 rounded-full px-4 py-1.5 mb-6">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        <span class="text-sm text-indigo-300">Platform CBT Terpercaya</span>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                        Ujian Online<br>
                        <span class="gradient-text">Aman & Terpercaya</span><br>
                        dengan AI Proctoring
                    </h1>
                    
                    <p class="text-lg text-slate-400 max-w-xl mb-8 leading-relaxed">
                        Platform Computer Based Test (CBT) dengan pengawasan kamera real-time, 
                        deteksi kecurangan otomatis, dan penilaian instan untuk menjamin 
                        integritas setiap ujian online.
                    </p>
                    
                    <div class="flex flex-wrap gap-4 mb-8">
                        <a href="{{ route('register') }}" 
                           class="group inline-flex items-center bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-3.5 rounded-xl font-semibold transition-all shadow-lg shadow-indigo-600/25 hover:shadow-indigo-500/40">
                            Mulai Sekarang
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <a href="{{ route('guide.download') }}" 
                           class="group inline-flex items-center bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white px-8 py-3.5 rounded-xl font-semibold transition-all">
                            <svg class="w-5 h-5 mr-2 text-indigo-400 group-hover:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Panduan Pengguna
                            <span class="ml-2 text-xs bg-indigo-500/20 text-indigo-300 px-2 py-0.5 rounded-full">PDF</span>
                        </a>
                    </div>

                    <!-- Trust badges -->
                    <div class="flex items-center space-x-6 text-sm text-slate-500">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Gratis</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Tanpa Setup Ribet</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>Aman</span>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Mockup -->
                <div class="animate-fade-in hidden lg:block">
                    <div class="relative">
                        <!-- Main mockup card -->
                        <div class="glass-card rounded-2xl p-1 shadow-2xl">
                            <div class="bg-slate-900 rounded-xl overflow-hidden">
                                <!-- Browser bar -->
                                <div class="flex items-center space-x-2 px-4 py-3 bg-slate-800/80 border-b border-slate-700/50">
                                    <div class="flex space-x-1.5">
                                        <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                                        <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                                        <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                                    </div>
                                    <div class="flex-1 mx-4">
                                        <div class="bg-slate-700/50 rounded-lg px-3 py-1 text-xs text-slate-400 text-center">
                                            zafproctor.app/ujian
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Exam interface mockup -->
                                <div class="p-5">
                                    <div class="flex justify-between items-center mb-4">
                                        <div>
                                            <div class="text-sm font-bold text-white">Ujian Matematika Dasar</div>
                                            <div class="text-xs text-slate-500">Soal 5 dari 20</div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 rounded-lg bg-green-500/20 border border-green-500/30 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <div class="bg-indigo-500/20 border border-indigo-500/30 px-3 py-1 rounded-lg">
                                                <span class="text-xs text-indigo-300 font-mono">01:23:45</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Question -->
                                    <div class="bg-slate-800/50 rounded-xl p-4 mb-4 border border-slate-700/30">
                                        <p class="text-sm text-slate-300 mb-3">Jika f(x) = 2x + 3 dan g(x) = x&#xB2; - 1, maka (f &#x2218; g)(2) = ...</p>
                                        <div class="space-y-2">
                                            <div class="flex items-center p-2.5 rounded-lg bg-slate-700/30 hover:bg-slate-700/50 cursor-pointer border border-transparent hover:border-slate-600/50 transition">
                                                <div class="w-6 h-6 rounded-full border-2 border-slate-600 mr-3 flex-shrink-0"></div>
                                                <span class="text-xs text-slate-400">A. 5</span>
                                            </div>
                                            <div class="flex items-center p-2.5 rounded-lg bg-indigo-500/10 border border-indigo-500/30">
                                                <div class="w-6 h-6 rounded-full border-2 border-indigo-500 mr-3 flex-shrink-0 bg-indigo-500 flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <span class="text-xs text-indigo-300 font-medium">B. 9</span>
                                            </div>
                                            <div class="flex items-center p-2.5 rounded-lg bg-slate-700/30 hover:bg-slate-700/50 cursor-pointer border border-transparent hover:border-slate-600/50 transition">
                                                <div class="w-6 h-6 rounded-full border-2 border-slate-600 mr-3 flex-shrink-0"></div>
                                                <span class="text-xs text-slate-400">C. 7</span>
                                            </div>
                                            <div class="flex items-center p-2.5 rounded-lg bg-slate-700/30 hover:bg-slate-700/50 cursor-pointer border border-transparent hover:border-slate-600/50 transition">
                                                <div class="w-6 h-6 rounded-full border-2 border-slate-600 mr-3 flex-shrink-0"></div>
                                                <span class="text-xs text-slate-400">D. 11</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Navigation dots -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex space-x-1">
                                            <div class="w-6 h-6 rounded bg-indigo-500 text-[9px] flex items-center justify-center text-white font-bold">1</div>
                                            <div class="w-6 h-6 rounded bg-indigo-500 text-[9px] flex items-center justify-center text-white font-bold">2</div>
                                            <div class="w-6 h-6 rounded bg-indigo-500 text-[9px] flex items-center justify-center text-white font-bold">3</div>
                                            <div class="w-6 h-6 rounded bg-indigo-500 text-[9px] flex items-center justify-center text-white font-bold">4</div>
                                            <div class="w-6 h-6 rounded bg-indigo-400 ring-2 ring-indigo-400/50 text-[9px] flex items-center justify-center text-white font-bold">5</div>
                                            <div class="w-6 h-6 rounded bg-slate-700 text-[9px] flex items-center justify-center text-slate-500">6</div>
                                            <div class="w-6 h-6 rounded bg-slate-700 text-[9px] flex items-center justify-center text-slate-500">7</div>
                                            <div class="text-xs text-slate-600 flex items-center ml-1">...</div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <div class="px-3 py-1.5 bg-slate-700 rounded-lg text-xs text-slate-400">Sebelumnya</div>
                                            <div class="px-3 py-1.5 bg-indigo-600 rounded-lg text-xs text-white">Selanjutnya</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating cards -->
                        <div class="absolute -top-4 -right-4 glass-card rounded-xl px-4 py-3 animate-float">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-white">Proctoring Aktif</div>
                                    <div class="text-[10px] text-green-400">Kamera terhubung</div>
                                </div>
                            </div>
                        </div>

                        <div class="absolute -bottom-6 -left-6 glass-card rounded-xl px-4 py-3 animate-float-delay">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-indigo-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-white">Auto-Grade</div>
                                    <div class="text-[10px] text-indigo-400">Nilai otomatis</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="relative py-16 border-y border-slate-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="stat-card text-center p-6 rounded-2xl bg-slate-900/50 border border-slate-800/50">
                    <div class="text-3xl md:text-4xl font-extrabold gradient-text mb-1">100%</div>
                    <div class="text-sm text-slate-500">Gratis Digunakan</div>
                </div>
                <div class="stat-card text-center p-6 rounded-2xl bg-slate-900/50 border border-slate-800/50">
                    <div class="text-3xl md:text-4xl font-extrabold text-indigo-400 mb-1">24/7</div>
                    <div class="text-sm text-slate-500">Akses Kapan Saja</div>
                </div>
                <div class="stat-card text-center p-6 rounded-2xl bg-slate-900/50 border border-slate-800/50">
                    <div class="text-3xl md:text-4xl font-extrabold text-purple-400 mb-1">Real-time</div>
                    <div class="text-sm text-slate-500">Monitoring Langsung</div>
                </div>
                <div class="stat-card text-center p-6 rounded-2xl bg-slate-900/50 border border-slate-800/50">
                    <div class="text-3xl md:text-4xl font-extrabold text-emerald-400 mb-1">Auto</div>
                    <div class="text-sm text-slate-500">Penilaian Otomatis</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="relative py-24 overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-600/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-600/5 rounded-full blur-3xl"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-flex items-center space-x-2 bg-indigo-500/10 border border-indigo-500/20 rounded-full px-4 py-1 mb-4">
                    <span class="text-sm text-indigo-400">Fitur Unggulan</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">
                    Semua yang Anda Butuhkan untuk<br>
                    <span class="gradient-text">Ujian Online yang Aman</span>
                </h2>
                <p class="text-slate-400 max-w-2xl mx-auto">
                    Dirancang khusus untuk menjaga keamanan, integritas, dan kemudahan pelaksanaan ujian online
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="feature-card group bg-slate-900/50 border border-slate-800/50 rounded-2xl p-6 transition-all duration-300 hover:border-indigo-500/30">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500/20 to-indigo-600/20 rounded-xl flex items-center justify-center mb-4 group-hover:shadow-lg group-hover:shadow-indigo-500/10 transition">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Pengawasan Webcam</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Snapshot otomatis webcam selama ujian untuk verifikasi identitas peserta secara berkala.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card group bg-slate-900/50 border border-slate-800/50 rounded-2xl p-6 transition-all duration-300 hover:border-emerald-500/30">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500/20 to-emerald-600/20 rounded-xl flex items-center justify-center mb-4 group-hover:shadow-lg group-hover:shadow-emerald-500/10 transition">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Deteksi Kecurangan</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Mendeteksi pergantian tab, keluar fullscreen, copy-paste, dan aktivitas mencurigakan lainnya.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card group bg-slate-900/50 border border-slate-800/50 rounded-2xl p-6 transition-all duration-300 hover:border-purple-500/30">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500/20 to-purple-600/20 rounded-xl flex items-center justify-center mb-4 group-hover:shadow-lg group-hover:shadow-purple-500/10 transition">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Timer Otomatis</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Durasi terkontrol dengan auto-submit saat waktu habis atau batas pelanggaran terlampaui.
                    </p>
                </div>
                
                <!-- Feature 4 -->
                <div class="feature-card group bg-slate-900/50 border border-slate-800/50 rounded-2xl p-6 transition-all duration-300 hover:border-amber-500/30">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500/20 to-amber-600/20 rounded-xl flex items-center justify-center mb-4 group-hover:shadow-lg group-hover:shadow-amber-500/10 transition">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Multi Tipe Soal</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Mendukung pilihan ganda dan essay dengan penilaian otomatis maupun manual oleh guru.
                    </p>
                </div>
                
                <!-- Feature 5 -->
                <div class="feature-card group bg-slate-900/50 border border-slate-800/50 rounded-2xl p-6 transition-all duration-300 hover:border-rose-500/30">
                    <div class="w-12 h-12 bg-gradient-to-br from-rose-500/20 to-rose-600/20 rounded-xl flex items-center justify-center mb-4 group-hover:shadow-lg group-hover:shadow-rose-500/10 transition">
                        <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Monitoring Real-time</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Guru dapat memantau semua peserta ujian secara langsung dalam tampilan grid interaktif.
                    </p>
                </div>
                
                <!-- Feature 6 -->
                <div class="feature-card group bg-slate-900/50 border border-slate-800/50 rounded-2xl p-6 transition-all duration-300 hover:border-sky-500/30">
                    <div class="w-12 h-12 bg-gradient-to-br from-sky-500/20 to-sky-600/20 rounded-xl flex items-center justify-center mb-4 group-hover:shadow-lg group-hover:shadow-sky-500/10 transition">
                        <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Laporan & Statistik</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Dashboard lengkap dengan statistik ujian, hasil peserta, log pelanggaran, dan export data.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section id="how-it-works" class="relative py-24 bg-slate-900/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-flex items-center space-x-2 bg-purple-500/10 border border-purple-500/20 rounded-full px-4 py-1 mb-4">
                    <span class="text-sm text-purple-400">Langkah Mudah</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">
                    Bagaimana <span class="gradient-text">Cara Kerjanya?</span>
                </h2>
                <p class="text-slate-400 max-w-2xl mx-auto">
                    Tiga langkah sederhana dari pembuatan ujian hingga hasil otomatis
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 relative">
                <!-- Connection line -->
                <div class="hidden md:block absolute top-16 left-[16.67%] right-[16.67%] h-0.5 bg-gradient-to-r from-indigo-500/50 via-purple-500/50 to-pink-500/50"></div>
                
                <!-- Step 1 -->
                <div class="relative text-center">
                    <div class="relative inline-flex mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-extrabold shadow-lg shadow-indigo-500/30 rotate-3 hover:rotate-0 transition-transform">
                            1
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3">Guru Membuat Ujian</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Buat ujian, tambahkan soal pilihan ganda atau essay, dan atur pengaturan proctoring sesuai kebutuhan.
                    </p>
                    <div class="mt-4 inline-flex items-center space-x-2 bg-indigo-500/10 rounded-xl px-4 py-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="text-xs text-indigo-300">Buat & Konfigurasi</span>
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div class="relative text-center">
                    <div class="relative inline-flex mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center text-white text-2xl font-extrabold shadow-lg shadow-purple-500/30 -rotate-3 hover:rotate-0 transition-transform">
                            2
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3">Siswa Mengerjakan</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Siswa masuk, verifikasi kamera, dan mengerjakan ujian dengan pengawasan otomatis yang berjalan di background.
                    </p>
                    <div class="mt-4 inline-flex items-center space-x-2 bg-purple-500/10 rounded-xl px-4 py-2">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs text-purple-300">Proctoring Aktif</span>
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="relative text-center">
                    <div class="relative inline-flex mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center text-white text-2xl font-extrabold shadow-lg shadow-pink-500/30 rotate-3 hover:rotate-0 transition-transform">
                            3
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3">Hasil Otomatis</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Nilai pilihan ganda otomatis, review essay oleh guru, dan laporan lengkap siap diunduh.
                    </p>
                    <div class="mt-4 inline-flex items-center space-x-2 bg-pink-500/10 rounded-xl px-4 py-2">
                        <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-xs text-pink-300">Laporan & Export</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Roles Section -->
    <section id="roles" class="relative py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-flex items-center space-x-2 bg-emerald-500/10 border border-emerald-500/20 rounded-full px-4 py-1 mb-4">
                    <span class="text-sm text-emerald-400">Tiga Peran Utama</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">
                    Dibuat untuk <span class="gradient-text">Semua Pengguna</span>
                </h2>
                <p class="text-slate-400 max-w-2xl mx-auto">
                    Setiap peran memiliki dashboard dan fitur yang dirancang khusus
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Admin -->
                <div class="group relative bg-slate-900/50 border border-slate-800/50 rounded-2xl p-8 hover:border-red-500/30 transition-all duration-300">
                    <div class="absolute top-0 right-0 px-4 py-1 bg-red-500/10 border-l border-b border-red-500/20 rounded-bl-xl rounded-tr-2xl">
                        <span class="text-xs text-red-400 font-semibold">ADMIN</span>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-red-500/20 to-orange-500/20 rounded-2xl flex items-center justify-center mb-6 group-hover:shadow-lg group-hover:shadow-red-500/10 transition">
                        <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Administrator</h3>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed">Kelola seluruh sistem termasuk pengguna, kelas, dan mata pelajaran.</p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-sm text-slate-300">
                            <svg class="w-4 h-4 text-red-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Manajemen pengguna & approval
                        </li>
                        <li class="flex items-center text-sm text-slate-300">
                            <svg class="w-4 h-4 text-red-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Kelola kelas & mata pelajaran
                        </li>
                        <li class="flex items-center text-sm text-slate-300">
                            <svg class="w-4 h-4 text-red-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Assign guru & siswa
                        </li>
                        <li class="flex items-center text-sm text-slate-300">
                            <svg class="w-4 h-4 text-red-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Dashboard statistik lengkap
                        </li>
                    </ul>
                </div>

                <!-- Teacher -->
                <div class="group relative bg-slate-900/50 border border-slate-800/50 rounded-2xl p-8 hover:border-blue-500/30 transition-all duration-300 md:-translate-y-4">
                    <div class="absolute top-0 right-0 px-4 py-1 bg-blue-500/10 border-l border-b border-blue-500/20 rounded-bl-xl rounded-tr-2xl">
                        <span class="text-xs text-blue-400 font-semibold">GURU</span>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500/20 to-cyan-500/20 rounded-2xl flex items-center justify-center mb-6 group-hover:shadow-lg group-hover:shadow-blue-500/10 transition">
                        <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Guru</h3>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed">Buat ujian, kelola soal, pantau peserta, dan lakukan penilaian.</p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-sm text-slate-300">
                            <svg class="w-4 h-4 text-blue-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Buat & kelola ujian
                        </li>
                        <li class="flex items-center text-sm text-slate-300">
                            <svg class="w-4 h-4 text-blue-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Bank soal PG & Essay
                        </li>
                        <li class="flex items-center text-sm text-slate-300">
                            <svg class="w-4 h-4 text-blue-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Monitoring real-time
                        </li>
                        <li class="flex items-center text-sm text-slate-300">
                            <svg class="w-4 h-4 text-blue-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Grading & export hasil
                        </li>
                        <li class="flex items-center text-sm text-slate-300">
                            <svg class="w-4 h-4 text-blue-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Import & duplikasi soal
                        </li>
                    </ul>
                </div>

                <!-- Student -->
                <div class="group relative bg-slate-900/50 border border-slate-800/50 rounded-2xl p-8 hover:border-emerald-500/30 transition-all duration-300">
                    <div class="absolute top-0 right-0 px-4 py-1 bg-emerald-500/10 border-l border-b border-emerald-500/20 rounded-bl-xl rounded-tr-2xl">
                        <span class="text-xs text-emerald-400 font-semibold">SISWA</span>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-2xl flex items-center justify-center mb-6 group-hover:shadow-lg group-hover:shadow-emerald-500/10 transition">
                        <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Siswa</h3>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed">Ikuti ujian online dengan mudah dan lihat hasil secara langsung.</p>
                    <ul class="space-y-3">
                        <li class="flex items-center text-sm text-slate-300">
                            <svg class="w-4 h-4 text-emerald-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Lihat daftar ujian tersedia
                        </li>
                        <li class="flex items-center text-sm text-slate-300">
                            <svg class="w-4 h-4 text-emerald-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Pre-check kamera otomatis
                        </li>
                        <li class="flex items-center text-sm text-slate-300">
                            <svg class="w-4 h-4 text-emerald-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Navigasi soal interaktif
                        </li>
                        <li class="flex items-center text-sm text-slate-300">
                            <svg class="w-4 h-4 text-emerald-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Lihat nilai & review jawaban
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Download Guide Banner -->
    <section class="relative py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 rounded-3xl p-10 md:p-14">
                <!-- Background decoration -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/3 -translate-x-1/4"></div>
                
                <div class="relative grid md:grid-cols-2 gap-8 items-center">
                    <div>
                        <div class="inline-flex items-center space-x-2 bg-white/10 rounded-full px-4 py-1 mb-4">
                            <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-white/90 text-sm font-medium">Panduan Lengkap</span>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-extrabold text-white mb-4">
                            Download Panduan Pengguna
                        </h3>
                        <p class="text-white/70 mb-6 leading-relaxed">
                            Panduan PDF lengkap untuk Admin, Guru, dan Siswa. 
                            Berisi langkah-langkah penggunaan, tips, screenshot, dan FAQ.
                        </p>
                        <a href="{{ route('guide.download') }}" 
                           class="group inline-flex items-center bg-white text-indigo-700 px-8 py-3.5 rounded-xl font-bold hover:bg-indigo-50 transition-all shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download PDF Gratis
                            <svg class="w-4 h-4 ml-2 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </a>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/10 backdrop-blur rounded-2xl p-6 border border-white/10">
                            <div class="text-center mb-4">
                                <div class="inline-flex items-center justify-center w-14 h-14 bg-white/20 rounded-xl mb-3">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div class="text-sm font-bold text-white">Isi Panduan</div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center space-x-3 bg-white/5 rounded-xl px-4 py-2.5">
                                    <div class="w-8 h-8 bg-red-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-semibold text-white">Panduan Admin</div>
                                        <div class="text-[10px] text-white/60">Kelola user, kelas, mapel</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 bg-white/5 rounded-xl px-4 py-2.5">
                                    <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-semibold text-white">Panduan Guru</div>
                                        <div class="text-[10px] text-white/60">Buat ujian, soal, monitoring</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 bg-white/5 rounded-xl px-4 py-2.5">
                                    <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-semibold text-white">Panduan Siswa</div>
                                        <div class="text-[10px] text-white/60">Ikuti ujian, kamera, hasil</div>
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
    <section class="relative py-24 overflow-hidden">
        <div class="absolute inset-0 hero-pattern"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-3xl"></div>
        
        <div class="relative max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6">
                Siap Memulai Ujian Online<br>
                <span class="gradient-text">yang Aman & Terpercaya?</span>
            </h2>
            <p class="text-lg text-slate-400 mb-10 max-w-2xl mx-auto">
                Daftar sekarang dan nikmati semua fitur ZAFProctor secara gratis. 
                Tidak perlu kartu kredit, langsung bisa digunakan.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('register') }}" 
                   class="group inline-flex items-center bg-indigo-600 hover:bg-indigo-500 text-white px-10 py-4 rounded-xl font-bold text-lg transition-all shadow-lg shadow-indigo-600/25 hover:shadow-indigo-500/40">
                    Daftar Gratis Sekarang
                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="{{ route('guide.download') }}" 
                   class="inline-flex items-center bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all">
                    <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Panduan PDF
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-800/50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xs">Z</span>
                        </div>
                        <span class="text-lg font-bold text-white">ZAF<span class="text-indigo-400">Proctor</span></span>
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Sistem ujian online dengan pengawasan kamera untuk menjamin integritas ujian.
                    </p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Platform</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-sm text-slate-500 hover:text-slate-300 transition">Fitur</a></li>
                        <li><a href="#how-it-works" class="text-sm text-slate-500 hover:text-slate-300 transition">Cara Kerja</a></li>
                        <li><a href="#roles" class="text-sm text-slate-500 hover:text-slate-300 transition">Peran Pengguna</a></li>
                        <li><a href="{{ route('guide.download') }}" class="text-sm text-slate-500 hover:text-slate-300 transition">Panduan Pengguna (PDF)</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-slate-300 mb-4">Akses</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('login') }}" class="text-sm text-slate-500 hover:text-slate-300 transition">Login</a></li>
                        <li><a href="{{ route('register') }}" class="text-sm text-slate-500 hover:text-slate-300 transition">Daftar</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800/50 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-slate-600">
                    &copy; {{ date('Y') }} ZAFProctor. All rights reserved.
                </p>
                <p class="text-sm text-slate-700 mt-2 md:mt-0">
                    Built with Laravel & Tailwind CSS
                </p>
            </div>
        </div>
    </footer>

    <!-- Navbar scroll effect -->
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('bg-slate-950/80', 'backdrop-blur-xl', 'border-b', 'border-slate-800/50');
            } else {
                navbar.classList.remove('bg-slate-950/80', 'backdrop-blur-xl', 'border-b', 'border-slate-800/50');
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
    </script>
</body>
</html>
