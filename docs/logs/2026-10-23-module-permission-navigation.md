# Permission modul dan visibilitas menu

## Perubahan

- Menu module launcher dan sidebar sekarang difilter berdasarkan permission akses modul (`access_company`, `access_crm`, `access_project_management`, `access_documents`) dan permission `view_any_*` per resource.
- Dashboard `/dashboard` sekarang memakai permission akses modul, sehingga kartu Document/CRM/Project/Company tidak muncul jika akses modul tidak dimiliki.
- Status modul aktif sekarang selalu menjadi syarat pertama pada dashboard, termasuk untuk super admin; super admin hanya membypass permission, bukan status nonaktif modul.
- Pengaturan dikecualikan dari status modul kerja: tetap tampil berdasarkan `access_settings` atau `super_admin`.
- Dashboard setiap modul juga melewati policy `viewAny`, sehingga akses URL langsung tidak dapat melewati menu.
- Policy Company, Vendor, CRM, Project/Ticket, Document, Document Type, dan Bundle menggunakan permission CRUD granular.
- Akses tidak lagi diberikan hanya karena user memiliki company scope atau role nama tertentu seperti `crm_admin`.
- Added regression test untuk user dengan akses dokumen saja: menu Company/CRM tidak tampil dan URL langsung ditolak.
- Added migration incremental untuk membuat permission akses modul dan backfill role lama dari permission `view_any_*` yang sudah ada.
- Memperbaiki pengelompokan permission pada form role dan pengguna: seluruh `access_*` masuk ke grup modul yang sesuai, bukan grup `Lainnya`.
- Ringkasan Document Management kini memakai `DocumentCompanyScope` untuk statistik tipe/dokumen/bundel dan daftar dokumen terbaru; tipe global tetap dihitung, sedangkan data perusahaan lain tidak.
- Migration master data `2026_10_23_000003_provision_basilindo_master_data` diperbarui agar deployment baru langsung membuat dan memberi permission akses modul; migration `000005` tetap menjadi backfill aman untuk database yang sudah berjalan.
- Scope perusahaan akun sistem diperketat: hanya Group Basilindo dan perusahaan internal aktif dengan parent Group Basilindo yang dipertahankan; migration `000006` mengoreksi akun sistem yang sudah telanjur memiliki perusahaan lain seperti TEST1.

## Verifikasi

- `php artisan test tests/Feature/ModulePermissionNavigationTest.php` — 2 test lulus, 13 assertion.
- Seluruh policy modul yang diubah lolos `php -l`.
- `php artisan view:cache` berhasil.
- `php artisan migrate --no-interaction` — migration akses modul berhasil.
- `git diff --check` — tidak ada whitespace error baru; hanya warning line ending pada perubahan lama.
- `php artisan test tests/Feature/ModulePermissionNavigationTest.php` — 3 test lulus, 19 assertion.
- `php artisan test tests/Feature/ModulePermissionNavigationTest.php` — 4 test lulus, 23 assertion setelah regresi status modul ditambahkan.
- `php artisan test tests/Feature/ModulePermissionNavigationTest.php` — 4 test lulus, 24 assertion setelah pengecualian Pengaturan ditambahkan.
