# Penyeragaman nama permission cetak HRIS

Tanggal: 2026-11-18

## Perubahan

- Menampilkan identifier permission teknis `print_hris_*` langsung pada form role dan user, konsisten dengan identifier seperti `view_hris_approvals`.
- Menambahkan migration rekonsiliasi master permission cetak HRIS yang memastikan permission tersedia dan grant awal role `super_admin`, `admin`, serta `hris_admin` tetap tersedia.
- Migration bersifat incremental dan tidak menghapus permission maupun grant yang telah diberikan.

## Verifikasi

- Menjalankan `php -l` pada migration.
- Menjalankan `php artisan view:cache`.
- Menjalankan `git diff --check`.

## Risiko tersisa

- Hak cetak Cash Advance, Lembur, dan Cuti tersedia di master permission; endpoint cetak untuk jenis tersebut belum menjadi bagian perubahan ini.
