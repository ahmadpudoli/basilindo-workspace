# Isolasi port Project Management

## Perubahan

- Laravel `php artisan serve` untuk `project-management` dikunci ke `localhost:8002`.
- Vite `project-management` dikunci ke `localhost:5175` dengan `strictPort: true`.
- `APP_URL` pada `.env` dan `.env.example` diarahkan ke `http://localhost:8002`.

## Verifikasi

- Konfigurasi Composer menjalankan Artisan pada port 8002.
- Konfigurasi Vite menggunakan port 5175.
- Jika port 5175 sedang dipakai proses lain, Vite akan berhenti dengan pesan konflik agar aplikasi tidak berpindah port tanpa sengaja.

## Risiko tersisa

- Jangan menjalankan mode `dev` dan `octane-dev` bersamaan karena keduanya menggunakan port aplikasi 8002.
