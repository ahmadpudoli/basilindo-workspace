# Perbaikan validasi role pada koneksi Core

## Perubahan

- Memperbaiki validasi nama role pada pembuatan dan perubahan role agar menggunakan koneksi serta tabel dari model `Core\\Models\\Role`.
- Memperbaiki validasi permission role agar menggunakan koneksi serta tabel dari model `Core\\Models\\Permission`.
- Menambahkan feature test untuk memastikan update role tidak lagi mencari tabel `ws_roles` pada koneksi default.

## Penyebab

Rule shorthand seperti `unique:roles,name` menggunakan koneksi default aplikasi. Pada runtime Basilindo, tabel authorization berada pada koneksi Core dengan prefix `core_`, sehingga query validasi gagal sebelum proses update berjalan.

## Verifikasi

- `php -l app/Modules/Core/Http/Controllers/AdministrationController.php`
- `php artisan test tests/Feature/RoleListTest.php`
- `git diff --check`

## Risiko tersisa

- Tidak ada migration yang diperlukan; perubahan hanya memperbaiki target koneksi pada validasi request.
