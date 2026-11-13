# 2026-11-12 — Master kategori biaya reimbursement

## Perubahan

- Menambahkan master kategori biaya HRIS, daftar awal kategori umum, dan backfill kategori yang sudah ada ke ID kategori tanpa mengubah snapshot nama lama.
- Mengubah kategori pada setiap rincian reimbursement menjadi dropdown master; kategori tidak aktif hanya dapat dipertahankan pada draft lama yang memang sudah memakainya.
- Menambahkan halaman Pengaturan → Pengaturan HRIS untuk tambah, edit, arsip, dan pemulihan kategori dengan pencarian, filter status, pagination, dan sorting.
- Mengamankan seluruh aksi master dengan `manage_hris_expense_categories` dan mencatat perubahan melalui audit logger.
- Menambahkan `manage_workspace_modules` untuk menu Modul aktif dan mengamankan halaman serta aksi penyimpanannya.
- Melengkapi katalog permission Core & Pengaturan yang sebelumnya hanya terbuat melalui seeder atau tidak tercantum pada Role; memindahkan Integrasi Odoo, Queue & Job, Master penomoran, Struktur organisasi, dan Pengaturan HRIS ke subgrup yang sesuai.
- Memperbarui requirement, checklist, peta dokumentasi, dan keputusan kategori melalui requirement master.

## Verifikasi

- Meninjau route, controller authorization, migration incremental, model, form request, layanan claim, Blade, dan pengelompokan Role/Pengguna.
- Test suite tidak dijalankan.

## Risiko tersisa

- Migrasi database dan tampilan aktual belum diverifikasi terhadap database runtime pada sesi ini.
- Master kategori bersifat global untuk semua perusahaan; kategori khusus perusahaan dapat ditambahkan melalui perluasan scope bila diperlukan.
