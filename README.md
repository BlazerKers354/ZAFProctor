# ZAFProctor - Sistem Ujian Online dengan Pengawasan Kamera

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.2-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

Sistem ujian online (Computer Based Test) berbasis web dengan pengawasan kamera (webcam proctoring) untuk meminimalkan kecurangan peserta ujian.

## 📋 Deskripsi

ZAFProctor adalah sistem ujian online yang dirancang untuk institusi pendidikan (sekolah/universitas). Sistem ini dilengkapi dengan fitur pengawasan kamera real-time yang dapat mendeteksi dan mencatat perilaku mencurigakan selama ujian berlangsung. Aplikasi ini mendukung multi-role (admin, guru, siswa) dengan sistem approval untuk pendaftaran pengguna baru, serta menyediakan panduan pengguna dalam format PDF yang dapat diunduh langsung dari halaman landing.

## 🌟 Fitur Utama

### 👨‍💼 Administrator
- Manajemen pengguna (CRUD admin, guru, siswa)
- Sistem approval/reject pendaftaran pengguna baru
- Manajemen kelas (kelompokkan siswa berdasarkan kelas, assign wali kelas)
- Manajemen mata pelajaran dengan guru pengampu
- Assign/hapus siswa ke kelas dan mata pelajaran
- Toggle status aktif/nonaktif pengguna
- Dashboard statistik

### 👨‍🏫 Guru
- Membuat dan mengelola ujian (scheduled & flexible)
- Membuat soal (pilihan ganda & essay) dengan urutan yang dapat diatur
- Import/Export soal dari template (download template tersedia)
- Duplikasi soal dan ujian
- Hapus soal secara massal (delete multiple)
- Mengatur jadwal dan durasi ujian
- Mengatur pengaturan proctoring per ujian (webcam, screen capture, browser lock)
- Publish/unpublish ujian
- Monitoring peserta ujian secara real-time (live monitoring)
- Melihat detail aktivitas peserta dan log pelanggaran dengan snapshot
- Terminate (paksa selesai) ujian peserta jika diperlukan
- Menilai jawaban essay (grading)
- Memberikan feedback pada hasil ujian
- Export hasil ujian
- Regenerate access token ujian

### 👨‍🎓 Siswa/Mahasiswa
- Verifikasi email sebelum dapat mengakses sistem
- Melihat daftar ujian yang tersedia dan riwayat ujian
- Pre-check kamera dan fullscreen sebelum ujian
- Memasukkan token akses dari guru untuk memulai ujian
- Mengerjakan ujian dengan pengawasan kamera (mode fullscreen)
- Navigasi soal dan auto-save jawaban
- Flag/bookmark soal untuk ditinjau ulang
- Sinkronisasi waktu dengan server (timer server-side, anti-manipulasi waktu)
- Offline queue: jawaban tersimpan lokal saat koneksi terputus, otomatis sinkron saat online
- Essay auto-save setiap 10 detik
- Dark mode dan pengaturan ukuran font (4 level) dalam antarmuka ujian
- Melihat hasil ujian dan detail jawaban (jika diizinkan guru)

### 📷 Fitur Proctoring
- **Pengawasan Webcam**: Snapshot otomatis selama ujian dengan interval yang dapat diatur
- **Deteksi Wajah (face-api.js)**: Deteksi keberadaan wajah peserta dengan peringatan countdown jika wajah tidak terdeteksi
- **Deteksi Multiple Wajah**: Mendeteksi jika lebih dari satu wajah terlihat di kamera
- **Browser Lock**: Mengunci browser agar tidak dapat melakukan aktivitas lain
- **Deteksi Tab Switch**: Mendeteksi ketika peserta berpindah tab (4 layer: visibilitychange, blur, focus monitoring, mouse leave)
- **Mode Fullscreen**: Memaksa peserta dalam mode fullscreen dengan keyboard lock (Escape)
- **Blokir Copy/Paste**: Mencegah aksi copy-paste (event listeners, Clipboard API override, execCommand override, Selection API override, drag & drop blocking)
- **Blokir Right Click**: Mencegah klik kanan (capture-phase pada document & window)
- **Blokir Keyboard Shortcut**: Mencegah shortcut keyboard terlarang (Ctrl, Alt, Meta, Function keys, PrintScreen)
- **Deteksi DevTools**: Mendeteksi pembukaan developer tools (3 metode: debugger timing, window size, console access)
- **Console Disable**: Menonaktifkan semua console method browser untuk mencegah manipulasi via console
- **Anti-Tampering**: Integrity check berkala memastikan fungsi anti-cheat tidak dimodifikasi atau dihapus
- **Print Blocking**: Mencegah aksi cetak halaman (beforeprint event + window.print override)
- **Picture-in-Picture Blocking**: Mencegah video PiP
- **Window.open Blocking**: Mencegah pembukaan window/tab baru via script
- **Middle-click Blocking**: Mencegah buka tab baru via middle-click
- **Pencatatan Pelanggaran**: Log semua aktivitas mencurigakan dengan severity level (low, medium, high)
- **Auto-Submit**: Otomatis kumpulkan ujian jika melebihi batas pelanggaran
- **Heartbeat System**: Monitoring koneksi peserta secara real-time
- **Rate Limiting**: Throttle pada endpoint kritis (start, save-answer, violation, snapshot, heartbeat)
- **Deteksi Manipulasi Waktu**: Validasi drift waktu client-server (>30 detik dicatat sebagai pelanggaran)

## 🛠️ Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade Templates + Bootstrap 5.3.2 (CDN) + Alpine.js
- **Ikon**: Phosphor Icons Web 2.1.1
- **Font**: Open Sans (dashboard) + Inter (auth)
- **Database**: MySQL
- **PDF Generation**: barryvdh/laravel-dompdf
- **Proctoring**: WebRTC (MediaDevices API) + face-api.js (TinyFaceDetector)
- **Build Tool**: Vite 5

## 📦 Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- MySQL
- Node.js & NPM
- Git

### Browser yang Didukung
Fitur proctoring (webcam) memerlukan browser modern dengan dukungan WebRTC:
- Google Chrome 60+ (Recommended)
- Mozilla Firefox 55+
- Microsoft Edge 79+
- Safari 11+

> ⚠️ **Penting**: Untuk mengakses webcam, aplikasi harus dijalankan melalui HTTPS atau localhost. Jika menggunakan domain, pastikan SSL sudah terpasang.

### Langkah Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/BlazerKers354/ZAFProctor.git
   cd ZAFProctor
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database di `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=zafproctor
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

   **Konfigurasi Email (untuk verifikasi & reset password)**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your_email@gmail.com
   MAIL_PASSWORD=your_app_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@zafproctor.test
   MAIL_FROM_NAME="ZAFProctor"
   ```

5. **Jalankan migrasi dan seeder**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Buat symbolic link untuk storage**
   ```bash
   php artisan storage:link
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Jalankan server**
   ```bash
   php artisan serve
   ```

9. **Akses aplikasi**
   ```
   http://localhost:8000
   ```

## 👤 Akun Default

| Role | Nama | Email | Password |
|------|------|-------|----------|
| Admin | Administrator | admin@zafproctor.test | password |
| Guru | Ibu Guru Demo | guru@zafproctor.test | password |
| Siswa | Ahmad Siswa | siswa1@zafproctor.test | password |
| Siswa | Budi Pelajar | siswa2@zafproctor.test | password |
| Siswa | Citra Murid | siswa3@zafproctor.test | password |

> **Catatan**: Semua akun seeder sudah dalam status aktif, disetujui, dan terverifikasi email. User baru yang mendaftar perlu diapprove oleh admin terlebih dahulu.

## 🔑 Fitur Authentication

- **Email Verification**: Pengguna harus memverifikasi email sebelum dapat mengakses sistem
- **Sistem Approval**: Admin dapat menyetujui/menolak pendaftaran user baru
- **Registrasi Terpisah**: Form registrasi berbeda untuk siswa dan guru
- **Forgot Password**: Reset password melalui email
- **Role-based Access**: Akses berbeda untuk admin, guru, dan siswa (middleware + policy)
- **Active Status**: Admin dapat menonaktifkan/mengaktifkan user

## 📐 Struktur Database

### Entity Relationship

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Roles     │────<│   Users     │>────│  Courses    │>────│   Exams     │
└─────────────┘     └─────┬───────┘     └─────────────┘     └─────┬───────┘
                          │                                       │
                    ┌─────▼─────┐                           ┌─────▼─────┐
                    │  Classes  │                           │ Questions │
                    └───────────┘                           └─────┬─────┘
                                                                  │
              ┌─────────────┐     ┌─────────────┐          ┌──────▼──────┐
              │ExamAttempts │────>│  Answers    │          │QuestionOptions│
              └─────┬───────┘     └─────────────┘          └─────────────┘
                    │
              ┌─────▼─────────┐     ┌─────────────┐
              │ProctoringLogs │     │ExamSettings │
              └───────────────┘     └─────────────┘
```

### Tabel Utama

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Data pengguna (admin, guru, siswa) dengan sistem approval |
| `roles` | Role pengguna (admin, teacher, student) |
| `classes` | Kelas siswa (1A, 1B, 2A, dll) dengan wali kelas |
| `courses` | Mata pelajaran/kuliah dengan guru pengampu |
| `course_student` | Relasi siswa-mata pelajaran |
| `exams` | Data ujian dengan tipe scheduled/flexible |
| `exam_settings` | Pengaturan proctoring lengkap per ujian |
| `questions` | Soal-soal ujian dengan urutan |
| `question_options` | Pilihan jawaban untuk pilihan ganda |
| `exam_attempts` | Record pengerjaan ujian dengan tracking pelanggaran |
| `answers` | Jawaban peserta |
| `proctoring_logs` | Log pelanggaran proctoring dengan severity |
| `audit_logs` | Log aktivitas sistem |

## 🔒 Keamanan Proctoring

Sistem mendeteksi dan mencatat berbagai jenis pelanggaran dengan tingkat severity:

| Kode | Pelanggaran | Severity | Deskripsi |
|------|-------------|----------|-----------|
| `tab_switch` | Tab Switch | High | Peserta berpindah ke tab lain |
| `window_blur` | Window Blur | Medium | Window kehilangan fokus |
| `fullscreen_exit` | Keluar Fullscreen | Medium | Peserta keluar dari mode fullscreen |
| `camera_disabled` | Kamera Dinonaktifkan | High | Akses kamera ditolak atau dimatikan |
| `no_face_detected` | Wajah Tidak Terdeteksi | Medium | Wajah peserta tidak terlihat di kamera |
| `multiple_faces` | Multiple Wajah | High | Lebih dari satu wajah terdeteksi di kamera |
| `copy_paste` | Copy/Paste | High | Aksi copy/paste terdeteksi |
| `keyboard_shortcut` | Shortcut Keyboard | Medium | Shortcut terlarang terdeteksi |
| `right_click` | Klik Kanan | Low | Klik kanan mouse |
| `browser_refresh` | Refresh Browser | Low | Peserta me-refresh halaman |
| `devtools` | Developer Tools | High | Pembukaan DevTools terdeteksi |
| `tampering` | Tampering | High | Fungsi anti-cheat dimodifikasi |

### Pengaturan Proctoring per Ujian

| Pengaturan | Default | Deskripsi |
|------------|---------|-----------|
| `webcam_enabled` | true | Mengaktifkan pengawasan webcam + deteksi wajah |
| `browser_lock_enabled` | true | Mengunci browser (fullscreen + deteksi keluar fullscreen) |
| `tab_switch_detection` | true | Mendeteksi perpindahan tab/window |
| `block_keyboard_shortcuts` | true | Blokir copy/paste, klik kanan, keyboard shortcut terlarang |
| `max_tab_switches` | 5 | Batas maksimal perpindahan tab |
| `snapshot_interval` | 30 | Interval snapshot dalam detik |
| `warning_threshold` | 3 | Jumlah pelanggaran sebelum peringatan intensif |
| `auto_submit_threshold` | 5 | Jumlah pelanggaran sebelum ujian auto-submit |
| `shuffle_questions` | false | Mengacak urutan soal |
| `shuffle_options` | false | Mengacak urutan pilihan jawaban |
| `show_score` | true | Menampilkan skor ke peserta |
| `show_correct_answers` | false | Menampilkan jawaban benar |
| `passing_score` | 60 | Nilai minimum kelulusan |
| `max_attempts` | 1 | Batas percobaan ujian (0 = unlimited) |
| `grade_method` | highest | Metode penilaian (highest/latest/average) |

## 📁 Struktur Project

```
zafproctor/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── ClassController.php      # Manajemen kelas
│   │   │   │   ├── CourseController.php     # Manajemen mata pelajaran
│   │   │   │   └── UserController.php       # Manajemen user + approval
│   │   │   ├── Auth/
│   │   │   │   ├── ForgotPasswordController.php
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── PasswordController.php
│   │   │   │   ├── RegisterController.php   # Registrasi siswa & guru
│   │   │   │   └── VerificationController.php  # Email verification
│   │   │   ├── Student/
│   │   │   │   ├── ExamController.php       # Mengerjakan ujian
│   │   │   │   └── ProctoringController.php # Logging pelanggaran & snapshot
│   │   │   ├── Teacher/
│   │   │   │   ├── ExamController.php       # CRUD ujian + grading + export
│   │   │   │   ├── MonitorController.php    # Monitoring real-time
│   │   │   │   └── QuestionController.php   # CRUD soal + import/export
│   │   │   ├── DashboardController.php      # Dashboard multi-role
│   │   │   ├── GuideController.php          # Download panduan PDF
│   │   │   ├── ProfileController.php
│   │   │   └── SnapshotController.php       # Serve snapshot proctoring
│   │   └── Middleware/
│   │       ├── CheckActiveUser.php          # Cek user aktif & approved
│   │       ├── EnsureExamInProgress.php     # Cek sesi ujian aktif
│   │       ├── LogActivity.php              # Logging aktivitas (audit log)
│   │       └── RoleMiddleware.php           # Autentikasi role
│   ├── Models/
│   │   ├── Answer.php
│   │   ├── AuditLog.php
│   │   ├── Course.php
│   │   ├── Exam.php                         # Tipe: scheduled/flexible
│   │   ├── ExamAttempt.php                  # Dengan feedback & tracking
│   │   ├── ExamSetting.php                  # Pengaturan proctoring lengkap
│   │   ├── ProctoringLog.php                # Dengan severity level
│   │   ├── Question.php
│   │   ├── QuestionOption.php
│   │   ├── Role.php
│   │   ├── SchoolClass.php                  # Manajemen kelas
│   │   └── User.php                         # Dengan sistem approval
│   ├── Policies/
│   │   ├── CoursePolicy.php
│   │   ├── ExamAttemptPolicy.php
│   │   ├── ExamPolicy.php
│   │   └── UserPolicy.php
│   └── Services/
│       ├── ExamService.php
│       └── ProctoringService.php            # Penanganan snapshot & log
├── database/
│   ├── migrations/                          # 22 migration files
│   └── seeders/
│       ├── ClassSeeder.php
│       ├── CourseSeeder.php
│       ├── DatabaseSeeder.php
│       ├── RoleSeeder.php
│       └── UserSeeder.php
├── resources/
│   └── views/
│       ├── landing.blade.php                # Halaman landing publik
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── classes/                     # CRUD + assign siswa
│       │   ├── courses/                     # CRUD + assign siswa
│       │   └── users/                       # CRUD + approval + pending
│       ├── auth/
│       │   ├── auth.blade.php               # Login & register
│       │   ├── forgot-password.blade.php
│       │   ├── reset-password.blade.php
│       │   └── verify-email.blade.php
│       ├── guides/
│       │   └── panduan-pengguna.blade.php   # Template PDF panduan
│       ├── layouts/
│       │   ├── admin.blade.php              # Layout panel admin
│       │   ├── app.blade.php                # Layout base
│       │   ├── exam.blade.php               # Layout pengerjaan ujian
│       │   ├── guest.blade.php              # Layout halaman tamu
│       │   ├── navigation.blade.php
│       │   ├── student.blade.php            # Layout panel siswa
│       │   └── teacher.blade.php            # Layout panel guru
│       ├── profile/
│       │   └── edit.blade.php
│       ├── student/
│       │   ├── dashboard.blade.php
│       │   └── exams/                       # Pre-check, take exam, result
│       └── teacher/
│           ├── dashboard.blade.php
│           ├── exams/                       # CRUD + grading + results
│           ├── monitor/                     # Live monitoring + logs
│           └── questions/                   # CRUD + import/export
└── routes/
    └── web.php                              # Semua route (public, auth, admin, teacher, student)
```

## 🧪 Testing

Jalankan test suite dengan perintah:

```bash
# Jalankan semua test
php artisan test

# Jalankan test dengan coverage
php artisan test --coverage

# Jalankan test spesifik
php artisan test --filter=ExamTest
```

## 🔧 Troubleshooting

### Kamera tidak terdeteksi
1. Pastikan browser memiliki izin akses kamera
2. Pastikan mengakses melalui HTTPS atau localhost
3. Coba refresh halaman dan izinkan kamera saat diminta

### Error "Mixed Content"
Jika menggunakan HTTPS, pastikan semua resource (gambar, API) juga menggunakan HTTPS.

### Migration gagal
```bash
php artisan migrate:fresh --seed
```

### Asset tidak muncul
```bash
npm run build
php artisan optimize:clear
```

## 🚀 Pengembangan Selanjutnya

- [x] ~~Integrasi face detection~~ (Terimplementasi menggunakan face-api.js)
- [x] ~~Dark mode~~ (Terimplementasi di antarmuka ujian)
- [ ] Halaman admin untuk melihat audit log aktivitas
- [ ] Real-time notification menggunakan WebSocket
- [ ] Bank soal dengan kategori
- [ ] Multi-language support
- [ ] Laporan analitik nilai siswa
- [ ] Deteksi Virtual Machine (VM)
- [ ] Watermark dinamis pada halaman ujian

## 📊 Fitur Akademis Krusial

### 1. **Sistem Ujian Online (CBT - Computer Based Test)**
Memungkinkan pelaksanaan ujian secara digital dengan fitur:
- Ujian terjadwal (scheduled) dan fleksibel
- Timer otomatis dengan sinkronisasi waktu server
- Auto-save jawaban untuk mencegah kehilangan data
- Multiple attempt dengan berbagai metode penilaian (highest/latest/average)
- Feedback guru pada hasil ujian

### 2. **Proctoring System (Sistem Pengawasan)**
Implementasi pengawasan ujian digital untuk menjaga integritas akademik:
- Webcam monitoring dengan snapshot berkala
- Face detection (deteksi keberadaan wajah & multiple wajah) menggunakan face-api.js
- Browser lockdown (fullscreen + keyboard lock + window.open block)
- Multi-layer tab switch detection (visibilitychange, blur, focus monitoring)
- Blokir copy/paste (event listener, Clipboard API, execCommand, Selection API, drag & drop)
- Blokir keyboard shortcut (Ctrl, Alt, Meta, Function keys, PrintScreen)
- Deteksi DevTools (debugger timing, window size, console access)
- Console disable & anti-tampering integrity monitor
- Logging pelanggaran dengan bukti screenshot dan severity level (low, medium, high)
- Rate limiting pada endpoint proctoring untuk mencegah abuse
- Deteksi manipulasi waktu client-server
- Offline answer queue dengan auto-sync

### 3. **Manajemen Akademik**
- Struktur hierarki: Kelas → Siswa → Mata Pelajaran → Ujian
- Wali kelas untuk setiap kelas
- Enrollment siswa ke mata pelajaran
- Registrasi terpisah untuk siswa dan guru

### 4. **Penilaian & Grading**
- Auto-grading untuk soal pilihan ganda
- Manual grading untuk soal essay
- Berbagai metode penilaian untuk multiple attempts
- Export hasil ujian
- Feedback per attempt

### 5. **Audit & Keamanan**
- Logging aktivitas pengguna (audit log via middleware)
- Sistem approval untuk pendaftaran baru
- Email verification
- Role-based access control dengan policy
- Rate limiting pada endpoint sensitif

### 6. **Panduan Pengguna (PDF)**
- Panduan lengkap untuk admin, guru, dan siswa
- Generate PDF otomatis menggunakan DomPDF
- Download langsung dari halaman landing (publik)

## 📄 Lisensi

MIT License - lihat file [LICENSE](LICENSE) untuk detail.

## 👥 Kontributor

- Zulfa Azka Farisadilah - Developer

---

**ZAFProctor** - Sistem Ujian Online dengan Pengawasan Kamera © 2026
