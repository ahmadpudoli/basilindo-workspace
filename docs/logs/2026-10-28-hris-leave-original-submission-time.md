# HRIS leave: pertahankan waktu pengajuan awal

## Perubahan

- Submit revisi tidak lagi mengubah `submitted_at`.
- Waktu submit revisi disimpan pada `revision_submitted_at`.
- Kartu ringkasan dan timeline `Pengajuan dibuat` memakai `created_at` sebagai
  waktu pengajuan awal, sehingga data lama yang pernah tertimpa tetap tampil
  berdasarkan waktu record dibuat.

## Verifikasi

- `php -l app/Modules/HRIS/Application/Services/LeaveRequestService.php`
- `git diff --check`
