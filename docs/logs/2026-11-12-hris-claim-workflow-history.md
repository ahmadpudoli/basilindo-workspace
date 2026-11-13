# HRIS Claim Workflow History

Tanggal: 2026-11-12

## Perubahan

- Sidebar timeline memakai seluruh `ApprovalEvent` workflow untuk menampilkan pengajuan awal, tiap keputusan/revisi, dan setiap pengiriman ulang dalam urutan waktu.
- Pemohon yang berhak melihat claim dapat melihat nama tahapan dan waktu event; identitas pelaku, komentar, dan snapshot tetap dilindungi permission audit trail.
- Requirement dan checklist diperbarui untuk membedakan riwayat workflow dari detail audit.

## Verifikasi

- `php artisan view:clear` dan `php artisan view:cache`.
- `php -l app/Modules/HRIS/Application/Services/HrisOperationsController.php`.
- `php -l app/Modules/HRIS/Domain/Models/Claim.php`.

## Risiko tersisa

- Test suite tidak dijalankan untuk perubahan tampilan timeline.
