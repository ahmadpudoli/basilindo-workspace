# Company storage key — 2026-10-13

## Perubahan

- Menambahkan `core_companies.storage_key` berupa ULID unik dan immutable.
- Perusahaan baru mendapatkan `storage_key` otomatis saat dibuat.
- Perusahaan existing mendapatkan nilai melalui migration backfill.
- Path dokumen memakai `storage_key`, bukan `code`, sehingga perubahan kode perusahaan tidak memindahkan atau memecah lokasi file.
- `storage_key` tidak ditampilkan atau diubah melalui form Master Perusahaan.

## Verifikasi

- Upload mengambil `storage_key` dari perusahaan internal yang dipilih.
- Test object path memastikan folder perusahaan menggunakan ULID 26 karakter.
