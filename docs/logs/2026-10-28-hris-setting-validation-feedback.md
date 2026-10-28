# HRIS setting: feedback validasi penyimpanan

## Perubahan

- Menambahkan panel warning pada halaman Pengaturan HRIS.
- Panel menampilkan status bahwa parameter belum disimpan dan daftar alasan
  validasi yang dikirim kembali oleh Laravel.
- Pesan dibuat lokal pada halaman Pengaturan HRIS agar tidak mengaktifkan lagi
  panel error global di seluruh halaman Workspace.

## Verifikasi

- `php artisan view:cache`
- `git diff --check`
