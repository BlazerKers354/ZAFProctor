# Deployment Production Guide

Dokumen ini berisi checklist agar ZAFProctor siap dihosting online dengan aman.

## 1. Persiapan Server

- PHP 8.2+ dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, `json`.
- Web server: Nginx atau Apache.
- Database: MySQL 8+ atau MariaDB 10.6+.
- Redis direkomendasikan untuk `cache`, `queue`, dan `session` pada trafik menengah-tinggi.
- SSL/TLS aktif (wajib untuk akses webcam browser).

## 2. Konfigurasi Environment

1. Salin file template produksi:

```bash
cp .env.production.example .env
```

2. Set nilai penting di `.env`:

- `APP_KEY`: generate dengan `php artisan key:generate`.
- `APP_URL`: harus `https://domain-anda`.
- `APP_DEBUG=false`.
- `SESSION_SECURE_COOKIE=true`.
- kredensial DB/Redis/Mail valid.

## 3. Build dan Migrasi

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

Catatan:
- Jalankan `db:seed` hanya jika memang dibutuhkan di production.
- Pastikan akun default dari seeder diubah/dinonaktifkan jika digunakan.

## 4. Optimasi Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## 5. Queue dan Scheduler

Jalankan worker queue secara daemon (contoh systemd/supervisor):

```bash
php artisan queue:work --queue=default --sleep=1 --tries=3 --timeout=90
```

Jalankan scheduler setiap menit di crontab:

```bash
* * * * * php /path/to/project/artisan schedule:run >> /dev/null 2>&1
```

## 6. Web Server

- Arahkan document root ke folder `public`.
- Aktifkan redirect HTTP -> HTTPS di level web server atau load balancer.
- Gunakan header keamanan tambahan di edge (opsional) jika sudah punya gateway/security appliance.
- Pastikan upload size kompatibel dengan snapshot webcam (disarankan `client_max_body_size 8M` di Nginx).

## 7. Hardening Operasional

- Nonaktifkan `APP_DEBUG`.
- Batasi akses dashboard admin hanya untuk akun internal.
- Rotasi password admin, database, SMTP secara berkala.
- Backup database terjadwal harian.
- Monitoring log `storage/logs` dan alert untuk error kritis.
- Gunakan WAF/rate-limiting di edge untuk endpoint login/register.

Tambahan hardening aplikasi:
- Middleware `ForceHttps`, `SecureHeaders`, dan `PreventSensitiveDataCaching` aktif di web stack.
- Endpoint terautentikasi mengirim header anti-cache (`Cache-Control: no-store`) untuk mencegah data sensitif tersimpan di browser/proxy.
- Endpoint proctoring snapshot hanya dapat diakses user yang lolos policy `reviewProctoring`.

## 8. Smoke Test Pasca Deploy

- Login admin/guru/siswa berhasil.
- Siswa dapat pre-check kamera dan masuk ujian via HTTPS.
- Snapshot proctoring tersimpan dan bisa diakses guru/admin.
- Autosave jawaban dan auto-submit berjalan saat threshold/time expired.
- Queue worker aktif tanpa backlog panjang.

## 9. Rollback Plan

- Siapkan backup database sebelum migrasi.
- Deploy dengan strategy zero/minimal downtime (blue/green atau rolling).
- Jika rollback aplikasi: pastikan kompatibilitas skema DB sebelum downgrade code.

## 10. Verifikasi Header Keamanan

Setelah deploy, verifikasi header menggunakan `curl -I`:

```bash
curl -I https://domain-anda/dashboard
curl -I https://domain-anda/student/exams
```

Minimum ekspektasi:
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: SAMEORIGIN`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy` berisi pembatasan kamera/microphone/geolocation
- `Cache-Control` mengandung `no-store` untuk halaman terautentikasi
- `Strict-Transport-Security` aktif saat request HTTPS

Jika menggunakan CSP, isi `SECURITY_CSP` di `.env` lalu ulangi verifikasi response header.

## 11. Release Readiness Gate

Jalankan checklist ini sebelum go-live:

```bash
php artisan test
npm run build
php artisan about --only=environment
```

Kriteria lulus:
- Semua test pass.
- Build frontend sukses tanpa error.
- Environment terbaca sebagai `production` saat verifikasi server production.
