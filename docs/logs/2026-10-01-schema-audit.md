# Audit schema database — 2026-10-01

## Perubahan

- Memeriksa migration, model, Resource/Page/Widget, service, route, test, dan konfigurasi
  driver Laravel.
- Memeriksa database PostgreSQL aktual `db_file_organize`: 39 tabel, 42 migration sudah
  berjalan, dan tidak ada tabel `saved_searches`.
- Menghapus orphan model `file-organizer/app/Models/SavedSearch.php`.
- Menambahkan requirement dan ADR untuk keputusan cleanup schema.

## Verifikasi

- `php artisan migrate:status --no-ansi` — seluruh migration terdaftar sebagai `Ran`.
- `php artisan db:show --counts --views --no-ansi` — database terhubung dan daftar tabel
  aktual cocok dengan schema yang dipertahankan.
- Pencarian consumer mengonfirmasi tabel ticket/project, permission, notification,
  external access, settings, dan domain dokumen masih digunakan.

## Risiko tersisa

- Tabel legacy ticket/project masih membuat schema lebih besar, tetapi menghapusnya
  sekarang akan merusak fitur aktif dan memerlukan migrasi data/fitur terpisah.
- Tabel framework fallback dapat kosong pada konfigurasi Redis aktif; jangan dihapus
  sebelum konfigurasi fallback dan jalur deployment dipensiunkan secara eksplisit.
