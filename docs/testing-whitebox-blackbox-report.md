# Laporan Whitebox dan Blackbox Testing ZAFProctor

Tanggal eksekusi: 2026-06-02

## Ringkasan Hasil

| Pemeriksaan | Hasil |
| --- | --- |
| PHPUnit/Laravel automated test | PASS, 59 test, 399 assertion |
| Regression route boot | PASS, `php artisan route:list`, 104 route |
| Smoke lokal landing/login/dashboard/pre-check | PASS via HTTP session terhadap server lokal |
| Browser in-app smoke | Tidak tersedia di sesi ini, daftar backend browser kosong |

Baseline sebelum penambahan test adalah 36 test dan 102 assertion. Setelah implementasi test komprehensif, suite bertambah menjadi 59 test dan 399 assertion.

## A. Whitebox Testing

Whitebox testing dilakukan dengan melihat struktur internal program, seperti model, service, policy, middleware, relasi data, dan logika perhitungan. Tujuannya adalah memastikan setiap cabang logika penting bekerja sesuai rancangan sistem.

File utama yang digunakan: `tests/Unit/ComprehensiveWhiteboxTest.php`.

### Fokus Pengujian Whitebox

| Komponen internal | Tujuan pengujian |
| --- | --- |
| Model `User` dan `Role` | Memastikan helper role, scope user, active/inactive account, dan approval guru berjalan benar |
| Model `Exam`, `ExamAttempt`, `ExamSetting` | Memastikan status ujian, jadwal, sisa waktu, batas pelanggaran, nilai, persentase, dan status lulus dihitung benar |
| Model `Question`, `Answer`, `ProctoringLog` | Memastikan tipe soal, normalisasi path gambar, grading pilihan ganda/esai, severity pelanggaran, dan URL snapshot benar |
| `ExamService` | Memastikan proses mulai ujian, batas percobaan, shuffle deterministik, validasi payload jawaban, submit, scoring, dan double submit |
| `ProctoringService` | Memastikan penyimpanan snapshot base64, counter pelanggaran, ringkasan pelanggaran, daftar snapshot, dan review log |
| Policy | Memastikan hak akses berdasarkan kepemilikan ujian, enrollment siswa, attempt owner, admin, dan guru |
| Middleware | Memastikan pembatasan role, user aktif, sesi ujian berjalan, header keamanan, cache control, dan redirect HTTPS produksi |

### Contoh Skenario Whitebox

| No | Skenario | Hasil yang diharapkan | Status |
| --- | --- | --- | --- |
| W-01 | User dengan role admin, teacher, dan student dicek melalui helper model | Method `isAdmin`, `isTeacher`, `isStudent`, dan `hasAnyRole` mengembalikan nilai sesuai role | PASS |
| W-02 | Guru pending approval disetujui oleh admin | `is_approved` menjadi true, `approved_by` dan `approved_at` terisi | PASS |
| W-03 | Ujian flexible dan scheduled dicek status aktifnya | Ujian published flexible aktif, scheduled aktif hanya dalam rentang waktu | PASS |
| W-04 | Attempt berjalan dan attempt kedaluwarsa dicek sisa waktunya | Attempt aktif memiliki remaining time, attempt lewat durasi bernilai 0 dan expired | PASS |
| W-05 | Jawaban pilihan ganda dan esai diberi nilai | Score, percentage, dan `is_passed` terhitung sesuai poin dan passing score | PASS |
| W-06 | Shuffle soal dan opsi dipanggil berulang pada attempt yang sama | Urutan soal konsisten untuk attempt yang sama | PASS |
| W-07 | Payload jawaban esai dikirim dengan option pilihan ganda | Service menolak payload yang tidak sesuai tipe soal | PASS |
| W-08 | Snapshot base64 valid disimpan oleh `ProctoringService` | File snapshot tersimpan dan path dikembalikan | PASS |
| W-09 | Pelanggaran tab switch dan fullscreen dicatat | `violation_count`, `tab_switch_count`, dan `fullscreen_exit_count` bertambah | PASS |
| W-10 | Policy akses diuji untuk guru pemilik, guru lain, admin, dan siswa | Akses hanya diberikan pada role/pemilik yang sesuai | PASS |
| W-11 | Middleware security header diuji | Header keamanan seperti `X-Frame-Options`, `Permissions-Policy`, dan HSTS terpasang | PASS |

### Hasil Whitebox

Seluruh skenario whitebox pada `ComprehensiveWhiteboxTest` berhasil dijalankan dengan hasil PASS. Pengujian ini membuktikan bahwa logika internal utama pada sistem ujian, penilaian, proctoring, akses, dan keamanan berjalan sesuai rancangan.

## B. Blackbox Testing

Blackbox testing dilakukan dari sudut pandang pengguna atau client tanpa melihat kode internal. Pengujian berfokus pada input, output, response halaman, redirect, validasi form, JSON API, session, dan perubahan data yang terlihat dari luar sistem.

File utama yang digunakan: `tests/Feature/AuthAccessBlackboxTest.php`, `tests/Feature/AdminManagementBlackboxTest.php`, `tests/Feature/TeacherWorkflowBlackboxTest.php`, dan `tests/Feature/StudentProctoringBlackboxTest.php`.

### Fokus Pengujian Blackbox

| Area pengguna/API | Tujuan pengujian |
| --- | --- |
| Public dan auth | Memastikan landing page, login, registrasi, reset password redirect, guide download, login gagal, dan logout bekerja |
| Role access | Memastikan guest diarahkan ke login dan role yang salah mendapatkan akses terlarang |
| Dashboard | Memastikan dashboard admin, guru, dan siswa dapat diakses user yang sesuai |
| Admin | Memastikan admin dapat mengelola user, approval guru, kelas, course, dan enrollment siswa |
| Guru | Memastikan guru dapat mengelola ujian, setting, publish, duplicate, export, soal, grading, dan monitoring |
| Siswa | Memastikan siswa dapat melihat ujian, pre-check, start exam, mengerjakan, menyimpan jawaban, submit, dan melihat hasil |
| Proctoring API | Memastikan endpoint setting, violation, heartbeat, snapshot, threshold, dan snapshot privat bekerja sesuai aturan |

### Contoh Skenario Blackbox

| No | Skenario | Input/Aksi | Hasil yang diharapkan | Status |
| --- | --- | --- | --- | --- |
| B-01 | Guest membuka dashboard | GET `/dashboard` tanpa login | Redirect ke halaman login | PASS |
| B-02 | Siswa membuka halaman admin | Login siswa lalu GET `/admin/users` | Response 403 forbidden | PASS |
| B-03 | Login guru aktif | Email dan password valid | Redirect ke dashboard | PASS |
| B-04 | Login user tidak aktif | Email dan password valid tetapi `is_active=false` | Login ditolak dengan error session | PASS |
| B-05 | Registrasi siswa | Data siswa dan password valid | User siswa dibuat, auto approved, dan login | PASS |
| B-06 | Registrasi guru | Data guru dan password valid | User guru dibuat dengan status pending approval | PASS |
| B-07 | Admin membuat user baru | Form user dengan role teacher/student | User tersimpan dan auto approved | PASS |
| B-08 | Admin mencoba membuat admin baru | Form user dengan role admin | Validasi role ditolak | PASS |
| B-09 | Admin menambah siswa ke kelas | `student_ids` valid | `class_id` siswa berubah sesuai kelas | PASS |
| B-10 | Admin menambah siswa ke course | `student_ids` valid | Relasi enrollment course tersimpan | PASS |
| B-11 | Guru membuat ujian | Payload ujian valid | Ujian tersimpan sebagai draft dan diarahkan ke halaman soal | PASS |
| B-12 | Guru publish ujian tanpa soal | Klik publish pada ujian kosong | Publish ditolak dengan pesan error | PASS |
| B-13 | Guru publish ujian dengan soal | Ujian memiliki minimal satu soal | Status ujian menjadi published | PASS |
| B-14 | Guru mengelola soal | Create, update, reorder, duplicate, delete | Perubahan soal tersimpan sesuai aksi | PASS |
| B-15 | Guru melakukan grading | Submit skor jawaban esai | Attempt menjadi graded dan score tersimpan | PASS |
| B-16 | Siswa start exam dengan token salah | Token tidak sesuai | Redirect ke pre-check dengan error token | PASS |
| B-17 | Siswa start exam tanpa pre-check kamera | Kamera wajib tetapi belum verified | Start ditolak dengan error pre-check | PASS |
| B-18 | Siswa menyimpan jawaban | `question_id` dan `option_id` valid | JSON success dan answer tersimpan | PASS |
| B-19 | Siswa submit ujian | POST submit attempt aktif | Redirect ke result dan attempt submitted | PASS |
| B-20 | Proctoring violation mencapai threshold | Dua violation pada threshold 2 | Response JSON `should_auto_submit=true` | PASS |
| B-21 | Snapshot invalid dikirim | Base64 tidak valid | Response 422 validation error | PASS |
| B-22 | Snapshot privat dibuka guru lain | Guru bukan pemilik ujian | Response 403 forbidden | PASS |

### Hasil Blackbox

Seluruh skenario blackbox berhasil dijalankan dengan hasil PASS. Pengujian ini menunjukkan bahwa fitur yang terlihat oleh pengguna, mulai dari autentikasi, role access, manajemen admin, pengelolaan ujian oleh guru, pengerjaan ujian oleh siswa, hingga endpoint proctoring, memberikan response yang sesuai dengan kebutuhan sistem.

## Command dan Bukti Eksekusi

Automated test:

```bash
php artisan test
```

Hasil:

```text
Tests: 59 passed (399 assertions)
Duration: 3.61s
```

Regression route boot:

```bash
php artisan route:list
```

Hasil: command exit code 0 dan menampilkan 104 route.

Smoke lokal:

```text
home_status: 200
admin_login_status: 302 -> /dashboard
admin_dashboard_status: 200
teacher_login_status: 302
teacher_dashboard_status: 200
student_login_status: 302
student_dashboard_status: 200
student_exams_status: 200
precheck_status: 200
smoke_exam_visible: true
```

Smoke memakai SQLite sementara di `storage/framework/testing/smoke.sqlite`, akun seed default, dan ujian fixture `Smoke Test Exam`. Server sementara dijalankan di `http://127.0.0.1:8010` lalu dihentikan setelah smoke selesai.

## Batasan

- Smoke browser visual tidak dapat dijalankan karena backend browser in-app tidak tersedia di sesi ini (`agent.browsers.list()` mengembalikan `[]`). Sebagai pengganti, smoke HTTP tetap memvalidasi server lokal, CSRF, cookie session, login multi-role, dashboard, daftar ujian, dan pre-check.
- Webcam fisik tidak diuji. Proctoring diuji melalui endpoint dan payload simulasi sesuai asumsi rencana.
- Dependency eksternal seperti Laravel framework, Dompdf, face-api.js, dan API browser tidak diuji sebagai unit milik proyek.
