# ZAFProctor - Sistem Ujian Online dengan Pengawasan Kamera

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-3.x-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

Sistem ujian online (Computer Based Test) berbasis web dengan pengawasan kamera (webcam proctoring) untuk meminimalkan kecurangan peserta ujian.

## 📋 Deskripsi

ZAFProctor adalah sistem ujian online yang dirancang untuk institusi pendidikan (sekolah/universitas). Sistem ini dilengkapi dengan fitur pengawasan kamera real-time yang dapat mendeteksi dan mencatat perilaku mencurigakan selama ujian berlangsung. Aplikasi ini mendukung multi-role dengan sistem approval untuk pendaftaran pengguna baru.

## 🌟 Fitur Utama

### 👨‍💼 Administrator
- Manajemen pengguna (CRUD admin, guru, siswa)
- Sistem approval pendaftaran pengguna baru
- Manajemen kelas (kelompokkan siswa berdasarkan kelas)
- Manajemen mata pelajaran/kuliah
- Assign siswa ke kelas dan mata pelajaran
- Melihat log aktivitas sistem
- Dashboard statistik

### 👨‍🏫 Guru
- Membuat dan mengelola ujian (scheduled & flexible)
- Membuat soal (pilihan ganda & essay)
- Import/Export soal dari template
- Duplikasi soal dan ujian
- Mengatur jadwal dan durasi ujian
- Mengatur pengaturan proctoring (webcam, screen capture, browser lock)
- Monitoring peserta ujian secara real-time
- Melihat log pelanggaran dengan snapshot
- Menilai jawaban essay
- Export hasil ujian
- Regenerate access token ujian

### 👨‍🎓 Siswa/Mahasiswa
- Verifikasi email sebelum dapat mengakses sistem
- Melihat daftar ujian yang tersedia
- Pre-check kamera dan fullscreen sebelum ujian
- Mengerjakan ujian dengan pengawasan kamera
- Melihat hasil ujian (jika diizinkan guru)

### 📷 Fitur Proctoring
- **Pengawasan Webcam**: Snapshot otomatis selama ujian dengan interval yang dapat diatur
- **Screen Capture**: Kemampuan menangkap layar peserta
- **Browser Lock**: Mengunci browser agar tidak dapat melakukan aktivitas lain
- **Deteksi Tab Switch**: Mendeteksi ketika peserta berpindah tab dengan batas maksimal
- **Mode Fullscreen**: Memaksa peserta dalam mode fullscreen
- **Blokir Copy/Paste**: Mencegah aksi copy-paste
- **Blokir Right Click**: Mencegah klik kanan
- **Blokir Keyboard Shortcut**: Mencegah shortcut keyboard terlarang
- **Pencatatan Pelanggaran**: Log semua aktivitas mencurigakan dengan severity level
- **Auto-Submit**: Otomatis kumpulkan ujian jika melebihi batas pelanggaran
- **Heartbeat System**: Monitoring koneksi peserta secara real-time

## 🛠️ Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
- **Database**: MySQL
- **Proctoring**: WebRTC (MediaDevices API)

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

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@zafproctor.test | password |
| Guru | guru@zafproctor.test | password |
| Siswa | siswa1@zafproctor.test | password |

> **Catatan**: User baru yang mendaftar perlu diapprove oleh admin terlebih dahulu, kecuali akun yang dibuat melalui seeder.

## 🔑 Fitur Authentication

- **Email Verification**: Pengguna harus memverifikasi email sebelum dapat mengakses sistem
- **Sistem Approval**: Admin dapat menyetujui/menolak pendaftaran user baru
- **Forgot Password**: Reset password melalui email
- **Role-based Access**: Akses berbeda untuk admin, guru, dan siswa
- **Active Status**: Admin dapat menonaktifkan user

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
| `class_student` | Relasi siswa-kelas dengan tahun ajaran |
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
| `tab_switch` | Tab Switch | Medium | Peserta berpindah ke tab lain |
| `window_blur` | Window Blur | Low | Window kehilangan fokus |
| `fullscreen_exit` | Keluar Fullscreen | High | Peserta keluar dari mode fullscreen |
| `camera_disabled` | Kamera Dinonaktifkan | Critical | Akses kamera ditolak atau dimatikan |
| `copy_paste` | Copy/Paste | Medium | Aksi copy/paste terdeteksi |
| `keyboard_shortcut` | Shortcut Keyboard | Low | Shortcut terlarang terdeteksi |
| `right_click` | Klik Kanan | Low | Klik kanan mouse |

### Pengaturan Proctoring per Ujian

| Pengaturan | Default | Deskripsi |
|------------|---------|-----------|
| `webcam_enabled` | true | Mengaktifkan pengawasan webcam |
| `screen_capture_enabled` | true | Mengaktifkan tangkapan layar |
| `browser_lock_enabled` | true | Mengunci browser dari aktivitas lain |
| `tab_switch_detection` | true | Mendeteksi perpindahan tab |
| `max_tab_switches` | 5 | Batas maksimal perpindahan tab |
| `snapshot_interval` | 30 | Interval snapshot dalam detik |
| `shuffle_questions` | false | Mengacak urutan soal |
| `shuffle_options` | false | Mengacak urutan pilihan jawaban |
| `show_score` | true | Menampilkan skor ke peserta |
| `show_correct_answers` | false | Menampilkan jawaban benar |
| `passing_score` | 60 | Nilai minimum kelulusan |
| `max_attempts` | null | Batas percobaan ujian |
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
│   │   │   │   ├── RegisterController.php
│   │   │   │   └── VerificationController.php  # Email verification
│   │   │   ├── Student/
│   │   │   │   ├── ExamController.php       # Mengerjakan ujian
│   │   │   │   └── ProctoringController.php # Logging pelanggaran
│   │   │   ├── Teacher/
│   │   │   │   ├── ExamController.php       # CRUD ujian + grading
│   │   │   │   ├── MonitorController.php    # Monitoring real-time
│   │   │   │   └── QuestionController.php   # CRUD soal + import/export
│   │   │   ├── DashboardController.php
│   │   │   └── ProfileController.php
│   │   └── Middleware/
│   │       ├── CheckActiveUser.php          # Cek user aktif & approved
│   │       ├── EnsureExamInProgress.php     # Cek sesi ujian aktif
│   │       ├── LogActivity.php              # Logging aktivitas
│   │       └── RoleMiddleware.php           # Autentikasi role
│   ├── Models/
│   │   ├── Answer.php
│   │   ├── AuditLog.php
│   │   ├── Course.php
│   │   ├── Exam.php                         # Tipe: scheduled/flexible
│   │   ├── ExamAttempt.php
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
│   ├── migrations/
│   └── seeders/
│       ├── ClassSeeder.php
│       ├── CourseSeeder.php
│       ├── DatabaseSeeder.php
│       ├── RoleSeeder.php
│       └── UserSeeder.php
├── resources/
│   └── views/
│       ├── admin/
│       │   ├── classes/                     # Manajemen kelas
│       │   ├── courses/
│       │   └── users/                       # + approval system
│       ├── auth/                            # Login, register, verify
│       ├── layouts/
│       ├── profile/
│       ├── student/
│       │   └── exams/                       # Pre-check, take exam, result
│       └── teacher/
│           ├── exams/                       # CRUD + grading
│           ├── monitor/                     # Real-time monitoring
│           └── questions/                   # CRUD + import/export
└── routes/
    └── web.php
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

- [ ] Integrasi face detection menggunakan TensorFlow.js
- [ ] Real-time notification menggunakan WebSocket
- [ ] Bank soal dengan kategori
- [ ] Multi-language support
- [ ] Dark mode
- [ ] Laporan analitik nilai siswa

## 📊 Fitur Akademis Krusial

### 1. **Sistem Ujian Online (CBT - Computer Based Test)**
Memungkinkan pelaksanaan ujian secara digital dengan fitur:
- Ujian terjadwal (scheduled) dan fleksibel
- Timer otomatis dengan sinkronisasi waktu server
- Auto-save jawaban untuk mencegah kehilangan data
- Multiple attempt dengan berbagai metode penilaian

### 2. **Proctoring System (Sistem Pengawasan)**
Implementasi pengawasan ujian digital untuk menjaga integritas akademik:
- Webcam monitoring dengan snapshot berkala
- Browser lockdown untuk mencegah kecurangan
- Deteksi aktivitas mencurigakan (tab switch, copy-paste, dll)
- Logging pelanggaran dengan bukti screenshot

### 3. **Manajemen Akademik**
- Struktur hierarki: Kelas → Siswa → Mata Pelajaran → Ujian
- Wali kelas untuk setiap kelas
- Enrollment siswa ke mata pelajaran

### 4. **Penilaian & Grading**
- Auto-grading untuk soal pilihan ganda
- Manual grading untuk soal essay
- Berbagai metode penilaian untuk multiple attempts
- Export hasil ujian

### 5. **Audit & Keamanan**
- Logging semua aktivitas pengguna
- Sistem approval untuk pendaftaran
- Email verification
- Role-based access control

## 📄 Lisensi

MIT License - lihat file [LICENSE](LICENSE) untuk detail.

## 👥 Kontributor

- Zulfa Azka Farisadilah - Developer

---

**ZAFProctor** - Sistem Ujian Online dengan Pengawasan Kamera © 2026
