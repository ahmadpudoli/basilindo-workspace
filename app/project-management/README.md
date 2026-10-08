# Basilindo Project Management

Aplikasi manajemen proyek internal Basilindo berbasis Laravel 12 dan Filament 4.

## Menjalankan aplikasi lokal

```bash
composer install
npm install
php artisan migrate
composer run dev
```

Aplikasi tersedia di `http://localhost:8002` dan Vite di `http://localhost:5175`.

Login admin dilakukan melalui Basilindo SSO menggunakan akun `admin@example.com`.

## Konfigurasi SSO

Salin `.env.example` menjadi `.env`, lalu isi `SSO_CLIENT_ID`, `SSO_CLIENT_SECRET`, dan URL callback sesuai registrasi client di `app/project-sso/`.

Project Management menggunakan session cookie terpisah agar logout aplikasi ini tidak menghapus session aplikasi Basilindo lain.
