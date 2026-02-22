<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Panduan Pengguna - ZAFProctor</title>
    <style>
        @page {
            margin: 2cm 2.5cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #1a1a2e;
        }

        /* Cover */
        .cover {
            text-align: center;
            padding-top: 120px;
            page-break-after: always;
        }
        .cover .logo-icon {
            width: 90px;
            height: 90px;
            background: #3b82f6;
            border-radius: 20px;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cover .logo-icon svg {
            width: 50px;
            height: 50px;
        }
        .cover h1 {
            font-size: 32pt;
            color: #3b82f6;
            margin-bottom: 5px;
        }
        .cover .subtitle {
            font-size: 16pt;
            color: #60a5fa;
            margin-bottom: 40px;
        }
        .cover .desc {
            font-size: 12pt;
            color: #64748b;
            max-width: 400px;
            margin: 0 auto 50px;
        }
        .cover .version {
            font-size: 10pt;
            color: #94a3b8;
        }

        /* TOC */
        .toc {
            page-break-after: always;
        }
        .toc h2 {
            font-size: 18pt;
            color: #3b82f6;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .toc-item {
            display: block;
            padding: 8px 0;
            border-bottom: 1px dotted #cbd5e1;
            color: #334155;
            text-decoration: none;
            font-size: 11pt;
        }
        .toc-item .page {
            float: right;
            color: #94a3b8;
        }
        .toc-section {
            font-weight: bold;
            font-size: 12pt;
            color: #3b82f6;
            margin-top: 15px;
        }

        /* Section headers */
        h2 {
            font-size: 18pt;
            color: #3b82f6;
            border-bottom: 3px solid #e0e7ff;
            padding-bottom: 8px;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        h3 {
            font-size: 14pt;
            color: #60a5fa;
            margin-top: 25px;
            margin-bottom: 12px;
        }
        h4 {
            font-size: 12pt;
            color: #1e293b;
            margin-top: 18px;
            margin-bottom: 8px;
        }

        /* Content */
        p {
            margin-bottom: 10px;
            text-align: justify;
        }
        ul, ol {
            margin-bottom: 12px;
            padding-left: 25px;
        }
        li {
            margin-bottom: 5px;
        }

        /* Info boxes */
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 12px 16px;
            margin: 15px 0;
            border-radius: 0 8px 8px 0;
            font-size: 10pt;
        }
        .info-box .label {
            font-weight: bold;
            color: #1d4ed8;
            margin-bottom: 5px;
        }
        .warning-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            margin: 15px 0;
            border-radius: 0 8px 8px 0;
            font-size: 10pt;
        }
        .warning-box .label {
            font-weight: bold;
            color: #b45309;
            margin-bottom: 5px;
        }
        .success-box {
            background: #ecfdf5;
            border-left: 4px solid #10b981;
            padding: 12px 16px;
            margin: 15px 0;
            border-radius: 0 8px 8px 0;
            font-size: 10pt;
        }
        .success-box .label {
            font-weight: bold;
            color: #047857;
            margin-bottom: 5px;
        }

        /* Step boxes */
        .step-container {
            margin: 15px 0;
        }
        .step {
            display: table;
            width: 100%;
            margin-bottom: 12px;
            background: #f8fafc;
            border-radius: 8px;
            overflow: hidden;
        }
        .step-number {
            display: table-cell;
            width: 45px;
            background: #3b82f6;
            color: white;
            text-align: center;
            vertical-align: middle;
            font-size: 16pt;
            font-weight: bold;
        }
        .step-content {
            display: table-cell;
            padding: 12px 16px;
            vertical-align: middle;
        }
        .step-content strong {
            color: #1e293b;
        }
        .step-content p {
            margin: 4px 0 0;
            font-size: 10pt;
            color: #64748b;
        }

        /* Illustration boxes */
        .illustration {
            text-align: center;
            margin: 20px 0;
            padding: 25px;
            background: #f1f5f9;
            border-radius: 12px;
            border: 2px dashed #cbd5e1;
        }
        .illustration .icon-large {
            font-size: 48pt;
            margin-bottom: 10px;
        }
        .illustration .caption {
            font-size: 10pt;
            color: #64748b;
            font-style: italic;
        }

        /* Screenshot placeholder */
        .screenshot {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            border: 2px solid #a5b4fc;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin: 15px 0;
        }
        .screenshot .mock-header {
            background: #3b82f6;
            color: white;
            padding: 8px 16px;
            border-radius: 6px 6px 0 0;
            font-size: 10pt;
            text-align: left;
            margin: -30px -30px 15px;
        }
        .screenshot .mock-body {
            background: white;
            border-radius: 0 0 8px 8px;
            padding: 20px;
            margin: 0 -30px -30px;
        }
        .screenshot .mock-nav {
            background: #f8fafc;
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            font-size: 9pt;
            color: #64748b;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10pt;
        }
        th {
            background: #3b82f6;
            color: white;
            padding: 10px 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:nth-child(even) td {
            background: #f8fafc;
        }

        /* Role badge */
        .role-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
            color: white;
            margin-right: 5px;
        }
        .role-admin { background: #ef4444; }
        .role-guru { background: #3b82f6; }
        .role-siswa { background: #10b981; }

        /* Page break */
        .page-break {
            page-break-after: always;
        }

        /* Footer */
        .footer-note {
            text-align: center;
            font-size: 9pt;
            color: #94a3b8;
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        /* Flow diagram */
        .flow-diagram {
            background: #f8fafc;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
        }
        .flow-step {
            background: white;
            border: 2px solid #e0e7ff;
            border-radius: 8px;
            padding: 10px 15px;
            margin: 8px 0;
            text-align: center;
        }
        .flow-arrow {
            text-align: center;
            color: #3b82f6;
            font-size: 16pt;
            margin: 3px 0;
        }

        /* Feature list */
        .feature-grid {
            display: table;
            width: 100%;
            margin: 15px 0;
        }
        .feature-item {
            display: table-row;
        }
        .feature-icon {
            display: table-cell;
            width: 40px;
            padding: 8px;
            vertical-align: top;
            text-align: center;
        }
        .feature-icon .circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-block;
            text-align: center;
            line-height: 32px;
            font-size: 14pt;
        }
        .feature-text {
            display: table-cell;
            padding: 8px 12px;
            vertical-align: top;
        }
        .feature-text strong {
            color: #1e293b;
        }
        .feature-text p {
            font-size: 10pt;
            color: #64748b;
            margin: 2px 0 0;
        }
    </style>
</head>
<body>

    <!-- ==================== COVER PAGE ==================== -->
    <div class="cover">
        <div style="width: 90px; height: 90px; margin: 0 auto 30px; text-align: center;">
            <svg width="90" height="90" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M30 5L52 17V43L30 55L8 43V17L30 5Z" fill="url(#gd_g1_cover)" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"/>
                <path d="M30 5L52 17L30 29L8 17L30 5Z" fill="url(#gd_g2_cover)" opacity="0.9"/>
                <path d="M30 29V55L8 43V17L30 29Z" fill="url(#gd_g3_cover)" opacity="0.7"/>
                <path d="M30 20L40 26V38L30 44L20 38V26L30 20Z" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>
                <defs>
                    <linearGradient id="gd_g1_cover" x1="8" y1="5" x2="52" y2="55"><stop stop-color="#3b82f6"/><stop offset="1" stop-color="#8b5cf6"/></linearGradient>
                    <linearGradient id="gd_g2_cover" x1="8" y1="5" x2="52" y2="29"><stop stop-color="#60a5fa"/><stop offset="1" stop-color="#a78bfa"/></linearGradient>
                    <linearGradient id="gd_g3_cover" x1="8" y1="17" x2="30" y2="55"><stop stop-color="#2563eb"/><stop offset="1" stop-color="#7c3aed"/></linearGradient>
                </defs>
            </svg>
        </div>
        <h1>ZAFProctor</h1>
        <div class="subtitle">Panduan Pengguna</div>
        <div class="desc">
            Panduan lengkap penggunaan Sistem Ujian Online dengan Pengawasan Kamera untuk Administrator, Guru, dan Siswa
        </div>

        <div style="margin: 40px auto; max-width: 420px;">
            <table style="border: none;">
                <tr>
                    <td style="text-align: center; padding: 12px; border: none;">
                        <div style="background: #fee2e2; width: 50px; height: 50px; border-radius: 50%; margin: 0 auto 8px; text-align: center; line-height: 50px;">
                            <span style="font-size: 20pt;">&#9881;</span>
                        </div>
                        <strong style="font-size: 9pt; color: #ef4444;">ADMIN</strong>
                    </td>
                    <td style="text-align: center; padding: 12px; border: none;">
                        <div style="background: #dbeafe; width: 50px; height: 50px; border-radius: 50%; margin: 0 auto 8px; text-align: center; line-height: 50px;">
                            <span style="font-size: 20pt;">&#9998;</span>
                        </div>
                        <strong style="font-size: 9pt; color: #3b82f6;">GURU</strong>
                    </td>
                    <td style="text-align: center; padding: 12px; border: none;">
                        <div style="background: #d1fae5; width: 50px; height: 50px; border-radius: 50%; margin: 0 auto 8px; text-align: center; line-height: 50px;">
                            <span style="font-size: 20pt;">&#9733;</span>
                        </div>
                        <strong style="font-size: 9pt; color: #10b981;">SISWA</strong>
                    </td>
                </tr>
            </table>
        </div>

        <div class="version">Versi 1.0 &bull; {{ date('F Y') }}</div>
    </div>

    <!-- ==================== TABLE OF CONTENTS ==================== -->
    <div class="toc">
        <h2>Daftar Isi</h2>
        
        <div class="toc-item toc-section">1. Pendahuluan</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;1.1 Tentang ZAFProctor</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;1.2 Persyaratan Sistem</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;1.3 Peran Pengguna</div>
        
        <div class="toc-item toc-section">2. Memulai - Registrasi & Login</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;2.1 Cara Mendaftar</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;2.2 Cara Login</div>
        
        <div class="toc-item toc-section">3. Panduan untuk Admin</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;3.1 Dashboard Admin</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;3.2 Manajemen Pengguna</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;3.3 Manajemen Kelas</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;3.4 Manajemen Mata Pelajaran</div>
        
        <div class="toc-item toc-section">4. Panduan untuk Guru</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;4.1 Dashboard Guru</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;4.2 Membuat Ujian Baru</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;4.3 Mengelola Soal</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;4.4 Monitoring Ujian</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;4.5 Penilaian & Hasil</div>
        
        <div class="toc-item toc-section">5. Panduan untuk Siswa</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;5.1 Dashboard Siswa</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;5.2 Mengerjakan Ujian</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;5.3 Proctoring & Aturan</div>
        <div class="toc-item">&nbsp;&nbsp;&nbsp;5.4 Melihat Hasil</div>
        
        <div class="toc-item toc-section">6. FAQ & Troubleshooting</div>
    </div>

    <!-- ==================== 1. PENDAHULUAN ==================== -->
    <h2>1. Pendahuluan</h2>

    <h3>1.1 Tentang ZAFProctor</h3>
    <p>
        <strong>ZAFProctor</strong> adalah platform ujian online (Computer Based Test / CBT) yang dilengkapi dengan 
        fitur pengawasan kamera (webcam proctoring) untuk menjamin integritas dan keamanan pelaksanaan ujian secara daring.
    </p>

    <div class="illustration">
        <div style="margin-bottom: 10px;">
            <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M30 5L52 17V43L30 55L8 43V17L30 5Z" fill="url(#gd_g1_illus)" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"/>
                <path d="M30 5L52 17L30 29L8 17L30 5Z" fill="url(#gd_g2_illus)" opacity="0.9"/>
                <path d="M30 29V55L8 43V17L30 29Z" fill="url(#gd_g3_illus)" opacity="0.7"/>
                <path d="M30 20L40 26V38L30 44L20 38V26L30 20Z" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.4)" stroke-width="1"/>
                <defs>
                    <linearGradient id="gd_g1_illus" x1="8" y1="5" x2="52" y2="55"><stop stop-color="#3b82f6"/><stop offset="1" stop-color="#8b5cf6"/></linearGradient>
                    <linearGradient id="gd_g2_illus" x1="8" y1="5" x2="52" y2="29"><stop stop-color="#60a5fa"/><stop offset="1" stop-color="#a78bfa"/></linearGradient>
                    <linearGradient id="gd_g3_illus" x1="8" y1="17" x2="30" y2="55"><stop stop-color="#2563eb"/><stop offset="1" stop-color="#7c3aed"/></linearGradient>
                </defs>
            </svg>
        </div>
        <div style="font-size: 14pt; font-weight: bold; color: #3b82f6;">ZAFProctor</div>
        <div style="font-size: 10pt; color: #64748b; margin-top: 5px;">Sistem Ujian Online dengan Pengawasan Kamera</div>
        <div style="margin-top: 15px;">
            <table style="max-width: 400px; margin: 0 auto; border: none;">
                <tr>
                    <td style="text-align: center; border: none; padding: 8px;">
                        <div style="background: #e0e7ff; border-radius: 8px; padding: 10px;">
                            <div style="font-size: 18pt;">&#128249;</div>
                            <div style="font-size: 8pt; color: #3b82f6; font-weight: bold;">Webcam Proctoring</div>
                        </div>
                    </td>
                    <td style="text-align: center; border: none; padding: 8px;">
                        <div style="background: #dcfce7; border-radius: 8px; padding: 10px;">
                            <div style="font-size: 18pt;">&#9989;</div>
                            <div style="font-size: 8pt; color: #16a34a; font-weight: bold;">Auto Grading</div>
                        </div>
                    </td>
                    <td style="text-align: center; border: none; padding: 8px;">
                        <div style="background: #fef3c7; border-radius: 8px; padding: 10px;">
                            <div style="font-size: 18pt;">&#128202;</div>
                            <div style="font-size: 8pt; color: #d97706; font-weight: bold;">Real-time Monitor</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <p>Fitur utama ZAFProctor meliputi:</p>
    <ul>
        <li><strong>Webcam Proctoring</strong> - Pengambilan snapshot otomatis selama ujian berlangsung</li>
        <li><strong>Deteksi Kecurangan</strong> - Mendeteksi pergantian tab, keluar fullscreen, dan aktivitas mencurigakan</li>
        <li><strong>Timer Otomatis</strong> - Auto-submit saat waktu habis atau batas pelanggaran terlampaui</li>
        <li><strong>Multi Tipe Soal</strong> - Pilihan ganda dan essay dengan penilaian otomatis/manual</li>
        <li><strong>Monitoring Real-time</strong> - Pantau peserta ujian secara langsung</li>
        <li><strong>Laporan Lengkap</strong> - Statistik ujian, hasil peserta, dan log pelanggaran</li>
    </ul>

    <h3>1.2 Persyaratan Sistem</h3>

    <table>
        <tr>
            <th style="width: 35%;">Persyaratan</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td><strong>Browser</strong></td>
            <td>Google Chrome (terbaru), Microsoft Edge (terbaru), atau Firefox (terbaru)</td>
        </tr>
        <tr>
            <td><strong>Webcam</strong></td>
            <td>Diperlukan untuk fitur proctoring (camera internal/external)</td>
        </tr>
        <tr>
            <td><strong>Koneksi Internet</strong></td>
            <td>Stabil, minimal 1 Mbps untuk pengiriman snapshot</td>
        </tr>
        <tr>
            <td><strong>Layar</strong></td>
            <td>Resolusi minimal 1024 x 768 piksel</td>
        </tr>
        <tr>
            <td><strong>JavaScript</strong></td>
            <td>Harus diaktifkan di browser</td>
        </tr>
    </table>

    <div class="warning-box">
        <div class="label">&#9888; Penting untuk Siswa</div>
        Pastikan webcam berfungsi dengan baik dan izin kamera telah diberikan kepada browser sebelum memulai ujian. Ujian dengan proctoring <strong>tidak dapat dimulai</strong> tanpa akses kamera.
    </div>

    <h3>1.3 Peran Pengguna</h3>
    <p>ZAFProctor memiliki tiga peran pengguna utama:</p>

    <table>
        <tr>
            <th>Peran</th>
            <th>Deskripsi</th>
            <th>Akses Utama</th>
        </tr>
        <tr>
            <td><span class="role-badge role-admin">Admin</span></td>
            <td>Mengelola seluruh sistem</td>
            <td>Pengguna, Kelas, Mata Pelajaran</td>
        </tr>
        <tr>
            <td><span class="role-badge role-guru">Guru</span></td>
            <td>Membuat & mengawasi ujian</td>
            <td>Ujian, Soal, Monitoring, Penilaian</td>
        </tr>
        <tr>
            <td><span class="role-badge role-siswa">Siswa</span></td>
            <td>Mengerjakan ujian</td>
            <td>Daftar Ujian, Mengerjakan, Hasil</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- ==================== 2. REGISTRASI & LOGIN ==================== -->
    <h2>2. Memulai - Registrasi & Login</h2>

    <h3>2.1 Cara Mendaftar</h3>

    <div class="illustration">
        <div style="background: white; border-radius: 10px; padding: 20px; max-width: 350px; margin: 0 auto; border: 1px solid #e2e8f0;">
            <div style="font-size: 14pt; font-weight: bold; color: #3b82f6; margin-bottom: 15px;">Formulir Pendaftaran</div>
            <div style="background: #f1f5f9; border-radius: 6px; padding: 8px 12px; margin-bottom: 8px; text-align: left; font-size: 9pt; color: #94a3b8;">Nama Lengkap</div>
            <div style="background: #f1f5f9; border-radius: 6px; padding: 8px 12px; margin-bottom: 8px; text-align: left; font-size: 9pt; color: #94a3b8;">Email</div>
            <div style="background: #f1f5f9; border-radius: 6px; padding: 8px 12px; margin-bottom: 8px; text-align: left; font-size: 9pt; color: #94a3b8;">Password</div>
            <div style="background: #f1f5f9; border-radius: 6px; padding: 8px 12px; margin-bottom: 15px; text-align: left; font-size: 9pt; color: #94a3b8;">Pilih Peran: Guru / Siswa</div>
            <div style="background: #3b82f6; color: white; border-radius: 6px; padding: 8px; font-size: 10pt; font-weight: bold;">Daftar</div>
        </div>
        <div class="caption">Tampilan form pendaftaran akun ZAFProctor</div>
    </div>

    <div class="step-container">
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-content">
                <strong>Buka Halaman Login/Daftar</strong>
                <p>Kunjungi halaman utama ZAFProctor dan klik tombol "Daftar".</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-content">
                <strong>Pilih Tipe Akun</strong>
                <p>Pilih mendaftar sebagai <strong>Guru</strong> atau <strong>Siswa</strong>. Akun Admin dibuat oleh sistem.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-content">
                <strong>Isi Data Diri</strong>
                <p>Masukkan nama lengkap, email, dan buat password yang kuat (minimal 8 karakter).</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">4</div>
            <div class="step-content">
                <strong>Tunggu Persetujuan</strong>
                <p>Akun baru memerlukan persetujuan Admin sebelum dapat digunakan.</p>
            </div>
        </div>
    </div>

    <div class="info-box">
        <div class="label">&#128161; Tips</div>
        Gunakan email yang valid dan aktif. Notifikasi persetujuan akun akan dikirim ke email yang didaftarkan.
    </div>

    <h3>2.2 Cara Login</h3>

    <div class="step-container">
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-content">
                <strong>Buka Halaman Login</strong>
                <p>Klik tombol "Login" di halaman utama ZAFProctor.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-content">
                <strong>Masukkan Kredensial</strong>
                <p>Masukkan email dan password yang telah didaftarkan.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-content">
                <strong>Masuk ke Dashboard</strong>
                <p>Setelah berhasil login, Anda akan diarahkan ke dashboard sesuai peran.</p>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- ==================== 3. PANDUAN ADMIN ==================== -->
    <h2>3. Panduan untuk Admin <span class="role-badge role-admin">Admin</span></h2>

    <h3>3.1 Dashboard Admin</h3>
    <p>Setelah login, Admin akan melihat dashboard yang menampilkan ringkasan sistem secara keseluruhan.</p>

    <div class="illustration">
        <div style="background: white; border-radius: 10px; padding: 0; max-width: 450px; margin: 0 auto; border: 1px solid #e2e8f0; overflow: hidden;">
            <div style="background: #1e293b; color: white; padding: 10px 15px; font-size: 10pt; text-align: left;">
                <span style="font-weight: bold; color: #818cf8;">ZAFProctor</span> &nbsp;&nbsp;
                <span style="color: #94a3b8;">Dashboard</span> &nbsp;
                <span style="color: #94a3b8;">Pengguna</span> &nbsp;
                <span style="color: #94a3b8;">Kelas</span> &nbsp;
                <span style="color: #94a3b8;">Mapel</span>
            </div>
            <div style="padding: 15px;">
                <div style="font-weight: bold; text-align: left; margin-bottom: 12px; color: #1e293b;">Dashboard Admin</div>
                <table style="border: none; margin: 0;">
                    <tr>
                        <td style="border: none; padding: 5px;">
                            <div style="background: #eff6ff; border-radius: 8px; padding: 10px; text-align: center;">
                                <div style="font-size: 18pt; font-weight: bold; color: #3b82f6;">24</div>
                                <div style="font-size: 8pt; color: #64748b;">Total User</div>
                            </div>
                        </td>
                        <td style="border: none; padding: 5px;">
                            <div style="background: #f0fdf4; border-radius: 8px; padding: 10px; text-align: center;">
                                <div style="font-size: 18pt; font-weight: bold; color: #22c55e;">6</div>
                                <div style="font-size: 8pt; color: #64748b;">Kelas</div>
                            </div>
                        </td>
                        <td style="border: none; padding: 5px;">
                            <div style="background: #fdf4ff; border-radius: 8px; padding: 10px; text-align: center;">
                                <div style="font-size: 18pt; font-weight: bold; color: #a855f7;">8</div>
                                <div style="font-size: 8pt; color: #64748b;">Mapel</div>
                            </div>
                        </td>
                        <td style="border: none; padding: 5px;">
                            <div style="background: #fef3c7; border-radius: 8px; padding: 10px; text-align: center;">
                                <div style="font-size: 18pt; font-weight: bold; color: #f59e0b;">3</div>
                                <div style="font-size: 8pt; color: #64748b;">Pending</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="caption">Ilustrasi tampilan Dashboard Admin</div>
    </div>

    <h3>3.2 Manajemen Pengguna</h3>
    <p>Admin dapat mengelola seluruh pengguna yang terdaftar di sistem:</p>

    <div class="feature-grid">
        <div class="feature-item">
            <div class="feature-icon">
                <div class="circle" style="background: #dbeafe; color: #3b82f6;">&#10003;</div>
            </div>
            <div class="feature-text">
                <strong>Menyetujui / Menolak Pendaftaran</strong>
                <p>Verifikasi dan approve akun guru serta siswa baru yang mendaftar.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon">
                <div class="circle" style="background: #dcfce7; color: #16a34a;">&#9998;</div>
            </div>
            <div class="feature-text">
                <strong>Edit Data Pengguna</strong>
                <p>Mengubah informasi pengguna seperti nama, email, dan peran.</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon">
                <div class="circle" style="background: #fee2e2; color: #dc2626;">&#10007;</div>
            </div>
            <div class="feature-text">
                <strong>Nonaktifkan / Hapus Akun</strong>
                <p>Menonaktifkan atau menghapus akun pengguna yang sudah tidak diperlukan.</p>
            </div>
        </div>
    </div>

    <div class="step-container">
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-content">
                <strong>Buka Menu "Pengguna"</strong>
                <p>Klik menu Pengguna di sidebar navigasi admin.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-content">
                <strong>Lihat Daftar Pengguna</strong>
                <p>Tampilan daftar semua user dengan filter berdasarkan peran dan status.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-content">
                <strong>Kelola Akun</strong>
                <p>Klik aksi pada setiap pengguna untuk approve, edit, atau nonaktifkan.</p>
            </div>
        </div>
    </div>

    <div class="info-box">
        <div class="label">&#128161; Persetujuan Pengguna</div>
        Akun baru yang mendaftar akan muncul di menu <strong>"Pending Approval"</strong> dan memerlukan persetujuan admin sebelum dapat digunakan.
    </div>

    <h3>3.3 Manajemen Kelas</h3>
    <p>Admin dapat membuat dan mengelola kelas serta menambahkan siswa ke dalam kelas:</p>

    <div class="step-container">
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-content">
                <strong>Buka Menu "Kelas"</strong>
                <p>Navigasi ke halaman manajemen kelas.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-content">
                <strong>Buat Kelas Baru</strong>
                <p>Klik "Tambah Kelas" dan isi nama kelas (contoh: X-IPA-1, XI-IPS-2).</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-content">
                <strong>Tambahkan Siswa</strong>
                <p>Pilih siswa yang akan dimasukkan ke dalam kelas tersebut.</p>
            </div>
        </div>
    </div>

    <h3>3.4 Manajemen Mata Pelajaran</h3>
    <p>Admin dapat membuat mata pelajaran dan menugaskan guru pengampu:</p>

    <div class="step-container">
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-content">
                <strong>Buka Menu "Mata Pelajaran"</strong>
                <p>Navigasi ke halaman manajemen mata pelajaran.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-content">
                <strong>Buat Mata Pelajaran</strong>
                <p>Isi nama mata pelajaran dan pilih guru pengampu.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-content">
                <strong>Assign Kelas</strong>
                <p>Tambahkan kelas-kelas yang mengambil mata pelajaran tersebut.</p>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- ==================== 4. PANDUAN GURU ==================== -->
    <h2>4. Panduan untuk Guru <span class="role-badge role-guru">Guru</span></h2>

    <h3>4.1 Dashboard Guru</h3>
    <p>Dashboard guru menampilkan ringkasan ujian yang telah dibuat, status ujian, dan statistik.</p>

    <div class="illustration">
        <div style="background: white; border-radius: 10px; padding: 0; max-width: 450px; margin: 0 auto; border: 1px solid #e2e8f0; overflow: hidden;">
            <div style="background: #1e293b; color: white; padding: 10px 15px; font-size: 10pt; text-align: left;">
                <span style="font-weight: bold; color: #818cf8;">ZAFProctor</span> &nbsp;&nbsp;
                <span style="color: #94a3b8;">Dashboard</span> &nbsp;
                <span style="color: #94a3b8;">Ujian</span> &nbsp;
                <span style="color: #94a3b8;">Monitoring</span>
            </div>
            <div style="padding: 15px;">
                <div style="font-weight: bold; text-align: left; margin-bottom: 12px; color: #1e293b;">Dashboard Guru</div>
                <table style="border: none; margin: 0;">
                    <tr>
                        <td style="border: none; padding: 5px;">
                            <div style="background: #eff6ff; border-radius: 8px; padding: 10px; text-align: center;">
                                <div style="font-size: 18pt; font-weight: bold; color: #3b82f6;">12</div>
                                <div style="font-size: 8pt; color: #64748b;">Total Ujian</div>
                            </div>
                        </td>
                        <td style="border: none; padding: 5px;">
                            <div style="background: #f0fdf4; border-radius: 8px; padding: 10px; text-align: center;">
                                <div style="font-size: 18pt; font-weight: bold; color: #22c55e;">3</div>
                                <div style="font-size: 8pt; color: #64748b;">Aktif</div>
                            </div>
                        </td>
                        <td style="border: none; padding: 5px;">
                            <div style="background: #fef3c7; border-radius: 8px; padding: 10px; text-align: center;">
                                <div style="font-size: 18pt; font-weight: bold; color: #f59e0b;">156</div>
                                <div style="font-size: 8pt; color: #64748b;">Peserta</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="caption">Ilustrasi tampilan Dashboard Guru</div>
    </div>

    <h3>4.2 Membuat Ujian Baru</h3>

    <div class="flow-diagram">
        <div class="flow-step"><strong>Buat Ujian</strong> - Isi detail ujian</div>
        <div class="flow-arrow">&#8595;</div>
        <div class="flow-step"><strong>Tambah Soal</strong> - PG / Essay</div>
        <div class="flow-arrow">&#8595;</div>
        <div class="flow-step"><strong>Atur Proctoring</strong> - Kamera & Deteksi</div>
        <div class="flow-arrow">&#8595;</div>
        <div class="flow-step"><strong>Publish</strong> - Ujian siap dikerjakan</div>
    </div>

    <div class="step-container">
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-content">
                <strong>Klik "Buat Ujian Baru"</strong>
                <p>Dari dashboard atau halaman daftar ujian, klik tombol untuk membuat ujian baru.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-content">
                <strong>Isi Informasi Ujian</strong>
                <p>Masukkan judul, mata pelajaran, kelas, durasi, jadwal mulai/selesai, dan deskripsi.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-content">
                <strong>Konfigurasi Pengaturan</strong>
                <p>Atur opsi acak soal, tampilkan hasil, batas pelanggaran, dan pengaturan proctoring.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">4</div>
            <div class="step-content">
                <strong>Simpan & Lanjutkan</strong>
                <p>Simpan ujian, lalu lanjutkan ke penambahan soal.</p>
            </div>
        </div>
    </div>

    <h3>4.3 Mengelola Soal</h3>
    <p>Guru dapat membuat soal dengan dua tipe:</p>

    <table>
        <tr>
            <th>Tipe Soal</th>
            <th>Deskripsi</th>
            <th>Penilaian</th>
        </tr>
        <tr>
            <td><strong>Pilihan Ganda</strong></td>
            <td>Soal dengan 2-5 opsi jawaban, satu jawaban benar</td>
            <td>Otomatis</td>
        </tr>
        <tr>
            <td><strong>Essay</strong></td>
            <td>Soal uraian dengan jawaban teks bebas</td>
            <td>Manual oleh guru</td>
        </tr>
    </table>

    <div class="success-box">
        <div class="label">&#10003; Fitur Canggih Pengelolaan Soal</div>
        <ul style="margin: 5px 0 0; padding-left: 20px;">
            <li><strong>Import massal</strong> - Upload soal dari file template</li>
            <li><strong>Export soal</strong> - Unduh bank soal dalam format file</li>
            <li><strong>Duplikasi soal</strong> - Salin soal untuk ujian lain</li>
            <li><strong>Reorder</strong> - Atur ulang urutan soal dengan drag & drop</li>
        </ul>
    </div>

    <h3>4.4 Monitoring Ujian</h3>
    <p>Saat ujian berlangsung, guru dapat memantau peserta secara real-time:</p>

    <div class="illustration">
        <div style="background: white; border-radius: 10px; padding: 0; max-width: 450px; margin: 0 auto; border: 1px solid #e2e8f0; overflow: hidden;">
            <div style="background: #1e293b; color: white; padding: 8px 15px; font-size: 10pt; text-align: left;">
                <span style="font-weight: bold;">&#128065; Monitoring Ujian - Matematika Dasar</span>
            </div>
            <div style="padding: 12px;">
                <table style="border: none; margin: 0;">
                    <tr>
                        <td style="border: none; padding: 4px; width: 33%;">
                            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 8px; text-align: center;">
                                <div style="width: 40px; height: 40px; background: #d1d5db; border-radius: 50%; margin: 0 auto 5px;"></div>
                                <div style="font-size: 8pt; font-weight: bold;">Ahmad</div>
                                <div style="font-size: 7pt; color: #16a34a;">&#9679; Online</div>
                            </div>
                        </td>
                        <td style="border: none; padding: 4px; width: 33%;">
                            <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 8px; text-align: center;">
                                <div style="width: 40px; height: 40px; background: #d1d5db; border-radius: 50%; margin: 0 auto 5px;"></div>
                                <div style="font-size: 8pt; font-weight: bold;">Budi</div>
                                <div style="font-size: 7pt; color: #d97706;">&#9888; 2 Pelanggaran</div>
                            </div>
                        </td>
                        <td style="border: none; padding: 4px; width: 33%;">
                            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 8px; text-align: center;">
                                <div style="width: 40px; height: 40px; background: #d1d5db; border-radius: 50%; margin: 0 auto 5px;"></div>
                                <div style="font-size: 8pt; font-weight: bold;">Citra</div>
                                <div style="font-size: 7pt; color: #16a34a;">&#9679; Online</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="caption">Ilustrasi tampilan Monitoring Peserta Ujian</div>
    </div>

    <p>Fitur monitoring meliputi:</p>
    <ul>
        <li><strong>Grid View</strong> - Tampilan kotak semua peserta dengan status real-time</li>
        <li><strong>Status Pelanggaran</strong> - Warna indikator untuk jumlah pelanggaran</li>
        <li><strong>Detail Peserta</strong> - Klik peserta untuk melihat detail aktivitas dan snapshot kamera</li>
        <li><strong>Terminate</strong> - Paksa selesaikan ujian peserta jika diperlukan</li>
    </ul>

    <h3>4.5 Penilaian & Hasil</h3>

    <div class="step-container">
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-content">
                <strong>Penilaian Otomatis (Pilihan Ganda)</strong>
                <p>Soal pilihan ganda dinilai otomatis berdasarkan kunci jawaban yang telah ditentukan.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-content">
                <strong>Penilaian Manual (Essay)</strong>
                <p>Buka halaman grading, baca jawaban siswa, dan berikan nilai untuk setiap soal essay.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-content">
                <strong>Export Hasil</strong>
                <p>Download hasil ujian dalam format file untuk arsip dan pelaporan.</p>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- ==================== 5. PANDUAN SISWA ==================== -->
    <h2>5. Panduan untuk Siswa <span class="role-badge role-siswa">Siswa</span></h2>

    <h3>5.1 Dashboard Siswa</h3>
    <p>Dashboard siswa menampilkan daftar ujian yang tersedia, ujian yang sedang berlangsung, dan riwayat ujian.</p>

    <div class="illustration">
        <div style="background: white; border-radius: 10px; padding: 0; max-width: 450px; margin: 0 auto; border: 1px solid #e2e8f0; overflow: hidden;">
            <div style="background: #1e293b; color: white; padding: 10px 15px; font-size: 10pt; text-align: left;">
                <span style="font-weight: bold; color: #818cf8;">ZAFProctor</span> &nbsp;&nbsp;
                <span style="color: #94a3b8;">Dashboard</span> &nbsp;
                <span style="color: #94a3b8;">Ujian Saya</span>
            </div>
            <div style="padding: 15px;">
                <div style="font-weight: bold; text-align: left; margin-bottom: 12px; color: #1e293b;">Selamat Datang, Siswa!</div>
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px; margin-bottom: 8px; text-align: left; font-size: 9pt;">
                    <strong style="color: #1e40af;">&#128221; Matematika Dasar</strong><br>
                    <span style="color: #64748b;">Mulai: 22 Feb 2026 - 08:00 | Durasi: 90 menit</span>
                </div>
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px; text-align: left; font-size: 9pt;">
                    <strong style="color: #166534;">&#9989; Bahasa Indonesia</strong><br>
                    <span style="color: #64748b;">Selesai | Nilai: 85/100</span>
                </div>
            </div>
        </div>
        <div class="caption">Ilustrasi tampilan Dashboard Siswa</div>
    </div>

    <h3>5.2 Mengerjakan Ujian</h3>

    <div class="flow-diagram">
        <div class="flow-step"><strong>Pilih Ujian</strong> - Dari daftar ujian tersedia</div>
        <div class="flow-arrow">&#8595;</div>
        <div class="flow-step"><strong>Pre-Check</strong> - Verifikasi kamera & browser</div>
        <div class="flow-arrow">&#8595;</div>
        <div class="flow-step"><strong>Masukkan Token</strong> - Kode akses dari guru</div>
        <div class="flow-arrow">&#8595;</div>
        <div class="flow-step"><strong>Mulai Ujian</strong> - Fullscreen & kamera aktif</div>
        <div class="flow-arrow">&#8595;</div>
        <div class="flow-step"><strong>Jawab Soal</strong> - Navigasi & jawab semua soal</div>
        <div class="flow-arrow">&#8595;</div>
        <div class="flow-step"><strong>Submit</strong> - Kirim jawaban</div>
    </div>

    <div class="step-container">
        <div class="step">
            <div class="step-number">1</div>
            <div class="step-content">
                <strong>Pilih Ujian yang Tersedia</strong>
                <p>Di dashboard, temukan ujian yang ingin dikerjakan dan klik untuk melihat detail.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-content">
                <strong>Pre-Check Sistem</strong>
                <p>Sistem akan memeriksa kompatibilitas browser dan akses kamera Anda.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-content">
                <strong>Masukkan Token Ujian</strong>
                <p>Masukkan kode token yang diberikan oleh guru untuk memulai ujian.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">4</div>
            <div class="step-content">
                <strong>Kerjakan Soal</strong>
                <p>Jawab semua soal dengan teliti. Gunakan navigasi soal untuk berpindah antar soal.</p>
            </div>
        </div>
        <div class="step">
            <div class="step-number">5</div>
            <div class="step-content">
                <strong>Submit Jawaban</strong>
                <p>Setelah selesai, klik "Submit" untuk mengirimkan jawaban Anda.</p>
            </div>
        </div>
    </div>

    <h3>5.3 Proctoring & Aturan Ujian</h3>
    <p>Selama ujian berlangsung, sistem proctoring akan aktif untuk menjaga integritas.</p>

    <div class="warning-box">
        <div class="label">&#9888; PERHATIAN - Aturan Selama Ujian</div>
        <ul style="margin: 5px 0 0; padding-left: 20px;">
            <li><strong>JANGAN</strong> berpindah tab atau jendela browser lain</li>
            <li><strong>JANGAN</strong> keluar dari mode fullscreen</li>
            <li><strong>JANGAN</strong> menutup atau menghalangi kamera</li>
            <li><strong>JANGAN</strong> menggunakan perangkat lain selama ujian</li>
            <li>Pastikan <strong>wajah terlihat jelas</strong> di kamera sepanjang ujian</li>
        </ul>
    </div>

    <p>Jenis pelanggaran yang dideteksi:</p>

    <table>
        <tr>
            <th>Pelanggaran</th>
            <th>Deskripsi</th>
            <th>Dampak</th>
        </tr>
        <tr>
            <td><strong>Tab Switch</strong></td>
            <td>Berpindah ke tab atau jendela lain</td>
            <td>Tercatat sebagai pelanggaran</td>
        </tr>
        <tr>
            <td><strong>Exit Fullscreen</strong></td>
            <td>Keluar dari mode layar penuh</td>
            <td>Tercatat sebagai pelanggaran</td>
        </tr>
        <tr>
            <td><strong>Copy/Paste</strong></td>
            <td>Mencoba menyalin atau menempel teks</td>
            <td>Aksi diblokir & tercatat</td>
        </tr>
        <tr>
            <td><strong>Batas Terlampaui</strong></td>
            <td>Jumlah pelanggaran melebihi batas</td>
            <td>Ujian otomatis di-submit</td>
        </tr>
    </table>

    <div class="info-box">
        <div class="label">&#128161; Tips Mengerjakan Ujian</div>
        <ul style="margin: 5px 0 0; padding-left: 20px;">
            <li>Pastikan koneksi internet stabil sebelum memulai</li>
            <li>Siapkan ruangan yang cukup terang untuk kamera</li>
            <li>Tutup semua aplikasi yang tidak diperlukan</li>
            <li>Jawaban tersimpan otomatis, tidak perlu khawatir kehilangan jawaban</li>
        </ul>
    </div>

    <h3>5.4 Melihat Hasil Ujian</h3>
    <p>Setelah ujian selesai dan guru telah menilai (untuk soal essay), siswa dapat melihat:</p>
    <ul>
        <li><strong>Nilai Total</strong> - Skor keseluruhan dari ujian</li>
        <li><strong>Detail Jawaban</strong> - Review jawaban yang benar dan salah (jika diizinkan guru)</li>
        <li><strong>Log Pelanggaran</strong> - Ringkasan aktivitas mencurigakan yang tercatat</li>
    </ul>

    <div class="page-break"></div>

    <!-- ==================== 6. FAQ ==================== -->
    <h2>6. FAQ & Troubleshooting</h2>

    <h3>Pertanyaan Umum</h3>

    <h4>Q: Browser apa yang direkomendasikan?</h4>
    <p><strong>A:</strong> Google Chrome versi terbaru sangat direkomendasikan untuk kompatibilitas terbaik dengan fitur webcam dan fullscreen.</p>

    <h4>Q: Bagaimana jika kamera tidak terdeteksi?</h4>
    <p><strong>A:</strong> Pastikan:</p>
    <ol>
        <li>Kamera tidak digunakan oleh aplikasi lain</li>
        <li>Izin kamera sudah diberikan di pengaturan browser</li>
        <li>Driver kamera terinstal dengan benar</li>
        <li>Coba restart browser dan ulangi</li>
    </ol>

    <h4>Q: Apa yang terjadi jika koneksi terputus saat ujian?</h4>
    <p><strong>A:</strong> Jawaban yang sudah disimpan tidak akan hilang. Setelah koneksi kembali, siswa dapat melanjutkan ujian selama waktu belum habis. Timer tetap berjalan di server.</p>

    <h4>Q: Bagaimana cara mereset password?</h4>
    <p><strong>A:</strong> Klik "Lupa Password" di halaman login, masukkan email terdaftar, dan ikuti instruksi yang dikirim ke email Anda.</p>

    <h4>Q: Apakah ujian otomatis ter-submit saat waktu habis?</h4>
    <p><strong>A:</strong> Ya, sistem akan otomatis mengumpulkan semua jawaban yang telah disimpan ketika waktu ujian berakhir.</p>

    <h4>Q: Bisakah guru melihat snapshot kamera siswa?</h4>
    <p><strong>A:</strong> Ya, guru dapat melihat snapshot yang diambil selama ujian melalui fitur monitoring dan review hasil ujian.</p>

    <div class="success-box">
        <div class="label">&#128222; Butuh Bantuan?</div>
        Jika Anda mengalami kendala yang tidak tercakup di panduan ini, silakan hubungi administrator sekolah atau kirim laporan melalui email yang terdaftar di sistem.
    </div>

    <!-- Footer -->
    <div class="footer-note">
        <strong>ZAFProctor</strong> - Sistem Ujian Online dengan Pengawasan Kamera<br>
        Panduan Pengguna v1.0 &bull; {{ date('F Y') }}<br>
        &copy; {{ date('Y') }} ZAFProctor. All rights reserved.
    </div>

</body>
</html>
