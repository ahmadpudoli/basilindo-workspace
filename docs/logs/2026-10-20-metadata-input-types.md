# Metadata input types

## Perubahan

- Menetapkan tipe input metadata yang mudah dipahami: Teks, Teks panjang, Angka, Angka desimal, Tanggal, Tanggal & waktu, dan Ya/Tidak.
- Form tambah/edit dokumen kini merender kontrol berdasarkan tipe metadata tersebut.
- Mandatory true/false diterapkan pada UI dan divalidasi ulang di server.
- Nilai boolean false tetap dianggap valid ketika field mandatory karena false adalah nilai yang sah.

## Verifikasi

- `php -l app/Modules/DocumentManagement/Http/Controllers/DocumentManagementController.php` berhasil.
- `npm.cmd run build` berhasil.
- `php artisan view:cache` berhasil.
- `git diff --check` berhasil.
