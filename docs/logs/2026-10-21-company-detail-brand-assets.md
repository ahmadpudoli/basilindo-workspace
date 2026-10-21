# Company detail dan brand assets

Tanggal: 2026-10-21

## Perubahan

- Memperluas halaman detail perusahaan dengan informasi master, struktur parent/anak, status, peran bisnis, visibilitas, dan brand preview.
- Menambahkan kolom metadata asset logo pada tabel perusahaan.
- Menambahkan upload logo pada form create/update dengan validasi JPG/PNG/WEBP maksimal 2 MB.
- Menyimpan logo pada private disk konfigurasi aplikasi dengan object key ber-prefix `app-company`.
- Menambahkan endpoint streaming privat untuk menampilkan logo setelah authorization Company policy.
- Upload logo baru membersihkan object logo lama setelah metadata baru tersimpan.
- Menata input logo sebagai panel kompak di bagian atas kanan form dan menambahkan preview gambar sebelum submit.
- Menetapkan standard pagination dan tabel compact untuk Workspace pada implementasi daftar perusahaan.

## Verifikasi

- PHP lint controller, model, dan migration.
- Migration dijalankan pada database development.
- Blade cache dan frontend build dijalankan.
- Test suite dijalankan untuk memastikan authorization dan upload dokumen tidak regresi.
