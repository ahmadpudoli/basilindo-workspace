# Perbaikan scope perusahaan pada daftar tipe dokumen

## Perubahan

- Daftar tipe dokumen sekarang membatasi hasil berdasarkan perusahaan yang dapat diakses user.
- Tipe global tetap dapat tampil.
- Tipe yang terkait langsung atau melalui tabel relasi `document_type_companies` hanya tampil jika perusahaannya berada dalam scope user.
- Ditambahkan feature test untuk memastikan tipe milik perusahaan lain tidak tampil.

## Verifikasi

- `php artisan test tests/Feature/DocumentTypeFormViewTest.php`
- `php -l app/Modules/DocumentManagement/Http/Controllers/DocumentManagementController.php`
- `git diff --check`
