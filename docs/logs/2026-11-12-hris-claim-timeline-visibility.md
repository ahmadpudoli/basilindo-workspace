# HRIS Claim Timeline Visibility

Tanggal: 2026-11-12

## Perubahan

- Sidebar timeline kini selalu tampil pada halaman detail bagi pengguna yang diizinkan melihat claim.
- Pengguna tanpa permission audit trail melihat tahapan dasar pengajuan, Atasan, Finance, dan persetujuan akhir tanpa komentar atau detail audit.
- Pengguna dengan `view_claim_audit_trail` tetap melihat aktivitas, komentar, dan snapshot revisi dalam timeline lengkap.
- Requirement dan checklist diperjelas sesuai batas akses tersebut.

## Verifikasi

- `php artisan view:cache`.
- `php -l app/Modules/HRIS/Application/Services/HrisOperationsController.php`.
- `php -l app/Modules/HRIS/Domain/Models/Claim.php`.

## Risiko tersisa

- Test suite tidak dijalankan untuk perubahan ini.

## Perbaikan lanjutan

- Directive Blade pada iterasi timeline status dirapikan ke blok multi-baris agar pasangan `@if`/`@endif` dikompilasi dengan benar.
- Cache view lama dibersihkan, lalu semua Blade view berhasil dikompilasi ulang menggunakan `php artisan view:clear` dan `php artisan view:cache`.
