# Pengelompokan permission HRIS dan master role permission

## Perubahan

- Menempatkan `access_hris`, permission approval/ringkasan HRIS, serta permission resource HRIS pada grup `HRIS` di form edit user.
- Menyamakan mapping form role dengan menampilkan permission `approve_hris_request` dan `manage_hris_settings` sebagai bagian dari grup HRIS.
- Menambahkan migration incremental `2026_10_28_000008_reconcile_hris_permission_catalog` yang idempotent untuk melengkapi katalog permission HRIS dan grant `hris_admin`, `hris_member`, serta `super_admin`.
- Menambahkan provisioning permission HRIS pada `RoleSeeder` agar database testing/disposable dan instalasi baru memiliki katalog yang sama.

## Verifikasi

- Memeriksa mapping group pada `user-form.blade.php` dan `role-form.blade.php`.
- Menjalankan lint PHP pada migration dan seeder.

## Risiko tersisa

- Migration baru bersifat additive; `down()` tidak menghapus data authorization agar rollback tidak menghilangkan grant atau permission yang mungkin sudah digunakan.
