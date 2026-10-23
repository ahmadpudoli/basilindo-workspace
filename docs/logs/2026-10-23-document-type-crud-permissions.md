# Konsistensi permission CRUD tipe dokumen

## Perubahan

- Policy tipe dokumen tidak lagi membatasi create, update, dan delete hanya untuk `super_admin`.
- Aksi sekarang menggunakan permission granular:
  - `create_document_type`
  - `view_document_type` dan `view_any_document_type`
  - `update_document_type`
  - `delete_document_type`
- `super_admin` tetap menjadi bypass permission standar.
- Ditambahkan test authorization CRUD.

## Verifikasi

- `php artisan test tests/Feature/DocumentTypeFormViewTest.php` — 7 test lulus, 33 assertion.
- `php -l app/Modules/DocumentManagement/Policies/DocumentTypePolicy.php`
- `git diff --check`
