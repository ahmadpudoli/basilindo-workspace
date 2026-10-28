# HRIS leave revision: feedback konflik tanggal

## Perubahan

- Menangkap `OverlappingLeaveRequest` pada endpoint pengiriman ulang revisi.
- Pengguna dikembalikan ke form revisi dengan input yang dipertahankan dan
  pesan pada field tanggal jika tanggal beririsan dengan pengajuan aktif lain.
- Perilaku submit pengajuan baru tetap menggunakan feedback validasi yang sama.

## Verifikasi

- `php -l app/Modules/HRIS/Application/Services/LeaveRequestController.php`
- `git diff --check`
