# Daftar pengguna Workspace

## Acceptance criteria

- Daftar pengguna mendukung pencarian nama/email, filter role, dan status akun.
- Pagination menggunakan pilihan 10, 25, 50, dan 100 baris per halaman dengan default 10.
- Filter, pagination, dan sorting/query state dipertahankan melalui query string.
- Kolom Nama, Email, dan Status dapat diurutkan secara server-side dengan indikator arah.
- Relasi user dengan karyawan ditampilkan sebagai kolom Karyawan pada tabel utama,
  termasuk nama karyawan dan NIP; tidak ada tabel relasi terpisah di bawah daftar.
- Kegagalan validasi saat membuat atau memperbarui pengguna kembali ke form dengan
  satu ringkasan error unik dan state invalid pada field yang relevan.
- Tombol tambah, edit, dan arsip hanya ditampilkan sesuai policy/permission.
- Akun sistem tidak menampilkan aksi edit atau arsip.
