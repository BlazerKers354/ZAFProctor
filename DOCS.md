# ZAFProctor - Sistem Ujian Online dengan Pengawasan Kamera

Sistem ujian online (Computer Based Test) berbasis web dengan pengawasan kamera (webcam proctoring) untuk meminimalkan kecurangan peserta ujian.

## 📋 Deskripsi

ZAFProctor adalah sistem ujian online yang dirancang untuk institusi pendidikan. Sistem ini dilengkapi dengan fitur pengawasan kamera real-time yang dapat mendeteksi dan mencatat perilaku mencurigakan selama ujian berlangsung.

## 🌟 Fitur Utama

### 👨‍💼 Administrator
- Manajemen pengguna (CRUD admin, dosen, mahasiswa)
- Manajemen mata kuliah
- Melihat log aktivitas sistem
- Dashboard statistik

### 👨‍🏫 Dosen
- Membuat dan mengelola ujian
- Membuat soal (pilihan ganda & essay)
- Mengatur jadwal dan durasi ujian
- Mengatur pengaturan proctoring
- Monitoring peserta ujian secara real-time
- Melihat log pelanggaran dengan snapshot
- Menilai jawaban essay

### 👨‍🎓 Mahasiswa
- Melihat daftar ujian yang tersedia
- Mengerjakan ujian dengan pengawasan kamera
- Melihat hasil ujian

### 📷 Fitur Proctoring
- **Pengawasan Webcam**: Snapshot otomatis selama ujian
- **Deteksi Tab Switch**: Mendeteksi ketika peserta berpindah tab
- **Mode Fullscreen**: Memaksa peserta dalam mode fullscreen
- **Blokir Copy/Paste**: Mencegah aksi copy-paste
- **Pencatatan Pelanggaran**: Log semua aktivitas mencurigakan
- **Auto-Submit**: Otomatis kumpulkan ujian jika melebihi batas pelanggaran

## 🛠️ Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
- **Database**: PostgreSQL
- **Proctoring**: WebRTC (MediaDevices API)

## 📦 Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- PostgreSQL
- Node.js & NPM
- Git

### Langkah Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/username/zafproctor.git
   cd zafproctor
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
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=zafproctor
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
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
| Dosen | dosen@zafproctor.test | password |
| Mahasiswa | mhs1@zafproctor.test | password |

## 📐 Struktur Database

### Entity Relationship

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Roles     │────<│   Users     │>────│  Courses    │
└─────────────┘     └─────────────┘     └─────────────┘
                           │                   │
                           │                   │
                    ┌──────┴──────┐            │
                    │             │            │
              ┌─────▼─────┐ ┌─────▼─────┐┌─────▼─────┐
              │ExamAttempts│ │course_student│  Exams  │
              └─────┬─────┘ └───────────┘└─────┬─────┘
                    │                          │
              ┌─────▼─────┐              ┌─────▼─────┐
              │  Answers  │              │ Questions │
              └───────────┘              └─────┬─────┘
                                               │
              ┌─────────────┐           ┌──────▼──────┐
              │ProctoringLogs│          │QuestionOptions│
              └─────────────┘           └─────────────┘
```

### Tabel Utama

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Data pengguna (admin, dosen, mahasiswa) |
| `roles` | Role pengguna |
| `courses` | Mata kuliah |
| `course_student` | Relasi mahasiswa-mata kuliah |
| `exams` | Data ujian |
| `questions` | Soal-soal ujian |
| `question_options` | Pilihan jawaban (untuk pilihan ganda) |
| `exam_attempts` | Record pengerjaan ujian |
| `answers` | Jawaban peserta |
| `proctoring_logs` | Log pelanggaran proctoring |
| `exam_settings` | Pengaturan proctoring per ujian |
| `audit_logs` | Log aktivitas sistem |

## 🔒 Keamanan Proctoring

Sistem mendeteksi dan mencatat berbagai jenis pelanggaran:

| Kode | Pelanggaran | Deskripsi |
|------|-------------|-----------|
| `tab_switch` | Tab Switch | Peserta berpindah ke tab lain |
| `window_blur` | Window Blur | Window kehilangan fokus |
| `fullscreen_exit` | Keluar Fullscreen | Peserta keluar dari mode fullscreen |
| `camera_disabled` | Kamera Dinonaktifkan | Akses kamera ditolak atau dimatikan |
| `copy_paste` | Copy/Paste | Aksi copy/paste terdeteksi |
| `keyboard_shortcut` | Shortcut Keyboard | Shortcut terlarang terdeteksi |
| `right_click` | Klik Kanan | Klik kanan mouse |

## 📁 Struktur Project

```
zafproctor/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── CourseController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Auth/
│   │   │   │   ├── ForgotPasswordController.php
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── PasswordController.php
│   │   │   │   └── RegisterController.php
│   │   │   ├── Student/
│   │   │   │   ├── ExamController.php
│   │   │   │   └── ProctoringController.php
│   │   │   ├── Teacher/
│   │   │   │   ├── ExamController.php
│   │   │   │   ├── MonitorController.php
│   │   │   │   └── QuestionController.php
│   │   │   ├── DashboardController.php
│   │   │   └── ProfileController.php
│   │   └── Middleware/
│   │       ├── CheckActiveUser.php
│   │       ├── EnsureExamInProgress.php
│   │       ├── LogActivity.php
│   │       └── RoleMiddleware.php
│   ├── Models/
│   │   ├── Answer.php
│   │   ├── AuditLog.php
│   │   ├── Course.php
│   │   ├── Exam.php
│   │   ├── ExamAttempt.php
│   │   ├── ExamSetting.php
│   │   ├── ProctoringLog.php
│   │   ├── Question.php
│   │   ├── QuestionOption.php
│   │   ├── Role.php
│   │   └── User.php
│   ├── Policies/
│   │   ├── CoursePolicy.php
│   │   ├── ExamAttemptPolicy.php
│   │   ├── ExamPolicy.php
│   │   └── UserPolicy.php
│   └── Services/
│       ├── ExamService.php
│       └── ProctoringService.php
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── CourseSeeder.php
│       ├── DatabaseSeeder.php
│       ├── RoleSeeder.php
│       └── UserSeeder.php
├── resources/
│   └── views/
│       ├── admin/
│       │   ├── courses/
│       │   └── users/
│       ├── auth/
│       ├── layouts/
│       ├── profile/
│       ├── student/
│       │   └── exams/
│       └── teacher/
│           ├── exams/
│           ├── monitor/
│           └── questions/
└── routes/
    └── web.php
```

## 🚀 Pengembangan Selanjutnya

- [ ] Integrasi face detection menggunakan TensorFlow.js
- [ ] Real-time notification menggunakan WebSocket
- [ ] Export hasil ujian ke Excel/PDF
- [ ] Randomize urutan soal
- [ ] Bank soal dengan kategori
- [ ] Multi-language support
- [ ] Dark mode

## 📄 Lisensi

MIT License - lihat file [LICENSE](LICENSE) untuk detail.

## 👥 Kontributor

- Zulfa Alfian - Developer

---

**ZAFProctor** - Sistem Ujian Online dengan Pengawasan Kamera © 2024
