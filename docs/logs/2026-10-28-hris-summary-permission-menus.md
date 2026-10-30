# HRIS summary: menu dan daftar berdasarkan permission

## Perubahan

- Menambahkan menu Ringkasan Reimbursement dan Ringkasan Cash Advance.
- Memperbaiki menu Ringkasan Lembur agar memakai mode summary yang benar.
- Menyamakan mode summary Cuti & Sakit, Lembur, Reimbursement, dan Cash
  Advance agar menampilkan daftar lintas karyawan dalam scope perusahaan user.
- Setiap daftar memeriksa permission summary khususnya; user tidak memperoleh
  akses tipe pengajuan lain hanya karena memiliki salah satu permission.
- Tombol membuat pengajuan disembunyikan pada halaman summary.

## Verifikasi

- `php -l app/Modules/HRIS/Application/Services/HrisOperationsController.php`
- `git diff --check`
