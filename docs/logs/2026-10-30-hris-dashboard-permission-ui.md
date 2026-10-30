# Dashboard approval dan grouping permission lintas modul

## Perubahan

- Mengganti label sidebar `Pegawai` menjadi `Daftar Karyawan`.
- Menambahkan early warning approval di dashboard HRIS dan dashboard personal
  ketika user memiliki `view_hris_approvals`; item memakai scope manager/HR yang
  sama dengan inbox approval dan memiliki tautan detail terotorisasi.
- Membuat partial permission assignment bersama untuk Role dan Edit Pengguna.
- Grouping permission diterapkan lintas Core, Company & Vendor, CRM, Project
  Management, Document Management, dan HRIS dengan subkelompok fitur serta
  jumlah permission yang lebih mudah dipindai.
- Menambahkan migration additive untuk merekonsiliasi katalog master permission
  lintas modul pada database existing.

## Verifikasi

- `php -l` dijalankan pada controller dashboard dan migration.
- `git diff --check` dijalankan.
