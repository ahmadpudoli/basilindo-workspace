# Loading pada pencatatan pembayaran reimbursement

- Popup pencatatan pembayaran menampilkan overlay loading setelah validasi client berhasil.
- Loading tetap aktif selama kompresi bukti gambar dan permintaan AJAX, lalu ditutup pada semua hasil response atau kegagalan koneksi.
- Validasi yang gagal tetap menghentikan submit tanpa menampilkan loading.
- Requirement dan checklist pencatatan pembayaran diperbarui.

Verifikasi:
- `php artisan view:cache`
- `git diff --check`
