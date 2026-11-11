# Log 2026-11-11 — Filter Jenis Form Master Penomoran

## Perubahan

- Menambahkan filter `Jenis form` pada daftar master penomoran Core.
- Mengubah filter `Perusahaan` dan `Jenis form` menjadi dropdown multi-select berbasis checkbox, bukan list yang selalu terbuka.
- Menambahkan validasi whitelist form key dan membatasi pilihan perusahaan pada scope perusahaan yang dapat diakses pengguna.
- Mempertahankan query string untuk pencarian, filter, sorting, dan pagination.

## Verifikasi

- `php -l app/Modules/Core/Http/Controllers/NumberingRuleController.php`
- `php artisan view:cache`
- `git diff --check` pada controller dan view terkait

## Risiko tersisa

- Belum dilakukan smoke test browser terhadap kombinasi filter pada server lokal.
