<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZAFProctor - Sistem Ujian Online dengan Pengawasan Kamera</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Figtree', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-indigo-600">ZAFProctor</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Login</a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                    Sistem Ujian Online dengan<br>
                    <span class="text-yellow-400">Pengawasan Kamera</span>
                </h1>
                <p class="text-xl text-indigo-100 max-w-3xl mx-auto mb-8">
                    Platform Computer Based Test (CBT) yang dilengkapi fitur webcam proctoring 
                    untuk meminimalkan kecurangan dan memastikan integritas ujian online.
                </p>
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('register') }}" 
                       class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Mulai Sekarang
                    </a>
                    <a href="#features" 
                       class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white/10 transition">
                        Lihat Fitur
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Wave decoration -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="#F9FAFB"/>
            </svg>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Fitur Unggulan</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Dirancang khusus untuk memastikan keamanan dan integritas ujian online
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Pengawasan Webcam</h3>
                    <p class="text-gray-600">
                        Snapshot otomatis webcam selama ujian berlangsung untuk memastikan identitas peserta.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Deteksi Kecurangan</h3>
                    <p class="text-gray-600">
                        Sistem mendeteksi pergantian tab, keluar fullscreen, dan aktivitas mencurigakan lainnya.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Timer Otomatis</h3>
                    <p class="text-gray-600">
                        Durasi ujian terkontrol dengan auto-submit ketika waktu habis atau batas pelanggaran terlampaui.
                    </p>
                </div>
                
                <!-- Feature 4 -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Berbagai Tipe Soal</h3>
                    <p class="text-gray-600">
                        Mendukung soal pilihan ganda dan essay dengan penilaian otomatis atau manual.
                    </p>
                </div>
                
                <!-- Feature 5 -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Monitoring Real-time</h3>
                    <p class="text-gray-600">
                        Pengawas dapat memonitor peserta ujian secara langsung dengan tampilan grid.
                    </p>
                </div>
                
                <!-- Feature 6 -->
                <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Laporan & Statistik</h3>
                    <p class="text-gray-600">
                        Dashboard lengkap dengan statistik ujian, hasil peserta, dan log pelanggaran.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Cara Kerja</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Tiga langkah mudah untuk memulai ujian online yang aman
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                        1
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Guru Membuat Ujian</h3>
                    <p class="text-gray-600">
                        Guru membuat ujian dengan soal-soal dan mengatur pengaturan proctoring sesuai kebutuhan.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                        2
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Siswa Mengerjakan</h3>
                    <p class="text-gray-600">
                        Siswa mengerjakan ujian dengan pengawasan kamera dan deteksi aktivitas mencurigakan.
                    </p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                        3
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Hasil Otomatis</h3>
                    <p class="text-gray-600">
                        Nilai otomatis untuk pilihan ganda dan log lengkap aktivitas untuk review.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-indigo-600 to-purple-600">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-white mb-4">
                Siap Memulai Ujian Online yang Aman?
            </h2>
            <p class="text-xl text-indigo-100 mb-8">
                Daftar sekarang dan mulai gunakan ZAFProctor untuk institusi Anda.
            </p>
            <a href="{{ route('register') }}" 
               class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                Daftar Gratis
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <span class="text-2xl font-bold text-white">ZAFProctor</span>
                    <p class="text-sm mt-1">Sistem Ujian Online dengan Pengawasan Kamera</p>
                </div>
                <div class="text-sm">
                    © {{ date('Y') }} ZAFProctor. All rights reserved.
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
