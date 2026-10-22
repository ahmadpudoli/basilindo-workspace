# Perbaikan scope perusahaan pada daftar dokumen

## Perubahan

- Memperbaiki `DocumentCompanyScope` agar `super_admin` tidak dibatasi oleh assignment perusahaan yang kebetulan dimilikinya.
- Menambahkan filter `Perusahaan operasional` pada daftar dokumen.
- Opsi filter dan opsi perusahaan pada form upload memakai `Company::operationalFor($user)`.
- Menolak create/update dokumen jika perusahaan operasional yang dikirim berada di luar scope pengguna.
- Daftar dokumen sekarang menyediakan upload action, multi-select perusahaan operasional, dropdown tahun dari tanggal dokumen, pilihan jumlah baris, dan pagination server-side dengan tabel compact.
- Menu navigasi upload terpisah dihapus karena akses upload tersedia langsung dari header daftar dokumen.

## Verifikasi

- Query database aktual menemukan dokumen perusahaan ID 4 dan sebelumnya mengembalikan 0 hasil untuk user super admin karena scope salah; setelah perbaikan scope mengembalikan 1 hasil.
- `php artisan view:cache` dan pemeriksaan syntax PHP berhasil.

## Data

- Tidak ada migration, reset database, atau penghapusan data.
