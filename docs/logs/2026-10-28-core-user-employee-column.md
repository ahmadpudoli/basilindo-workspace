# Kolom Relasi Pengguna dan Karyawan

## Perubahan

- Menampilkan nama karyawan dan NIP pada kolom `Karyawan` di tabel utama
  Pengguna & Akses.
- Menghapus panel tabel relasi karyawan terpisah di bawah daftar.
- Tetap memakai `employeesByUserId` yang sudah dimuat controller untuk
  menghindari query tambahan per baris.
- Menambahkan satu panel ringkasan error validasi unik pada form pengguna serta
  state `is-invalid` untuk field identitas dan password.

## Verifikasi

- Acceptance criteria diperbarui pada `docs/requirements/core-user-list.md`.
