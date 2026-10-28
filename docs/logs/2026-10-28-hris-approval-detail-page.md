# HRIS approval: halaman detail terpisah

## Perubahan

- Menambahkan route detail approval berdasarkan jenis pengajuan, ID, dan tahap
  workflow.
- Notifikasi approver sekarang membawa metadata target approval dan membuka
  halaman tersebut; notifikasi pemohon tetap membuka detail pengajuan biasa.
- Tombol Detail pada tabel Persetujuan HRIS menjadi link halaman, bukan modal.
- Halaman detail approval menampilkan informasi pengajuan dan tindakan Setujui,
  Tolak, serta Minta revisi pada halaman yang sama.
- Akses halaman memeriksa permission approval, scope perusahaan, approver tahap,
  dan status workflow yang sesuai.

## Verifikasi

- `php -l app/Modules/HRIS/Application/Services/HrisOperationsController.php`
- `php -l app/Modules/Core/Http/Controllers/NotificationController.php`
- `git diff --check`
