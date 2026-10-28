# HRIS approval: aksi detail memakai modal

## Perubahan

- Aksi Tolak dan Minta revisi pada detail approval dipindahkan ke modal popup
  dengan field alasan, jenis revisi, dan panduan collapsible.
- Setelah keputusan workflow tersimpan, controller mengarahkan pengguna ke
  daftar Persetujuan HRIS agar tidak kembali ke detail yang sudah berubah
  statusnya dan menghasilkan 404.

## Verifikasi

- `php -l app/Modules/HRIS/Application/Services/HrisOperationsController.php`
- `git diff --check`
