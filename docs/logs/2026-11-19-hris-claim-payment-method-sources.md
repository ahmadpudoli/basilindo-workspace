# Sumber penerima pembayaran reimbursement

Tanggal: 2026-11-19

## Perubahan

- Transfer menggunakan snapshot rekening reimbursement aktif karyawan; endpoint menolak transfer jika rekening aktif tidak ada.
- Tunai dan kas kecil menyimpan nama karyawan sebagai penerima.
- Kas kecil meminta sumber dana teks bebas maksimal 150 karakter, disimpan pada klaim. Sistem tidak mengelola saldo atau mutasi kas kecil.
- Form menampilkan rekening aktif secara aman (nama pemilik, bank, dan nomor tersamarkan); nomor rekening penuh tidak dikirim ke HTML dan nilai pembayaran ditentukan ulang di server.

## Verifikasi

- `php -l` berhasil untuk controller, model klaim, dan migration.
- Migration tambahan diterapkan pada database lokal; status migration selesai.
- `php artisan view:cache` dijalankan untuk mengompilasi template.
- Test aplikasi tidak dijalankan.
