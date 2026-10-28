# Bypass scope daftar karyawan pada Biodata personal

Tanggal: 2026-10-28

## Perubahan

- Halaman Biodata personal tidak lagi bergantung pada scope akses daftar
  karyawan atau scope perusahaan pengguna.
- Data tetap dibatasi berdasarkan `employee.user_id` akun login dan record yang
  sudah soft-deleted tetap tidak ditampilkan.
- Menambahkan feature test untuk user tanpa relasi company scope khusus.

## Verifikasi

- Acceptance test Biodata memverifikasi halaman dapat dibuka dan menampilkan
  karyawan yang terhubung dengan akun login.
