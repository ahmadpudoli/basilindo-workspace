# Perbaikan validasi database upload dokumen

## Perubahan

- Memperbaiki rule `exists` pada create, edit, dan bundling dokumen agar memakai koneksi database yang sesuai:
  - `core.companies`
  - `core.projects`
  - `documents.document_types`
- Menghindari Laravel menambahkan prefix koneksi default `ws_` ke nama tabel `core_companies`, yang sebelumnya menyebabkan pencarian tabel `ws_core_companies`.
- Tidak ada migration, reset database, penghapusan tabel, atau penghapusan data.

## Verifikasi

- `php artisan route:list --path=workspace/documents`
- `php artisan test --filter=DocumentUploadServiceTest` — 8 test lulus, 17 assertion.

## Risiko tersisa

- Validasi controller lain yang memakai rule `exists:core_companies` di modul lama perlu dirapikan secara bertahap bila endpoint tersebut digunakan kembali.
