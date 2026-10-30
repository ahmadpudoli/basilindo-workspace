# HRIS: halaman summary cuti dan lembur terpisah

## Perubahan

- Menambahkan route dan halaman khusus `Ringkasan Cuti Karyawan`.
- Menambahkan route dan halaman khusus `Ringkasan Lembur Karyawan`.
- Halaman operasional Cuti & Sakit dan Lembur tidak lagi menjadi target menu
  summary.
- Halaman summary memiliki metrik ringkas, filter/list pengajuan, pagination,
  dan authorization berdasarkan permission summary serta scope perusahaan.

## Verifikasi

- `php -l app/Modules/HRIS/Application/Services/LeaveRequestController.php`
- `php -l app/Modules/HRIS/Application/Services/HrisOperationsController.php`
- `php artisan route:list --path=workspace/hris`
- `git diff --check`
