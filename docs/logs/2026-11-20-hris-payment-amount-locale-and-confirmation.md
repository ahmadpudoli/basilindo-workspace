# Format dan konfirmasi selisih nominal pembayaran

- Input nominal pembayaran memakai format lokal Indonesia `id-ID` dengan dua desimal, dan nilai terstandardisasi dikirim ke server.
- Saat diedit, pemisah ribuan hilang dan karakter selain digit serta satu koma desimal disaring; format `id-ID` diterapkan kembali saat fokus keluar.
- Selisih terhadap nominal pengajuan ditampilkan sebagai informasi tepat di bawah input.
- Saat menyimpan nominal yang berbeda, pengguna diminta mengonfirmasi melalui modal konfirmasi Workspace sebelum loading dan permintaan AJAX dimulai.
- Validasi server tetap mewajibkan nominal positif, meminta penanda konfirmasi, dan menolak selisih nominal tanpa konfirmasi pengguna.
- Requirement dan checklist pencatatan pembayaran diperbarui.

Verifikasi:
- `php artisan view:cache`
- `php -l app/Modules/HRIS/Application/Services/HrisOperationsController.php`
- `git diff --check`
