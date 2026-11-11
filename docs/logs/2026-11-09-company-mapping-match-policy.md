# Log 2026-11-09 — Kebijakan pencocokan mapping perusahaan

## Perubahan

- Mapping Odoo ke Workspace tetap menggunakan external key: source system, source database, entity type, dan external ID.
- Nama perusahaan yang sama hanya digunakan untuk menampilkan kandidat mapping unik.
- Kandidat tidak dianggap mapped sampai user menyimpan pilihan melalui halaman mapping.
- Sistem menolak penggunaan satu perusahaan Workspace untuk dua perusahaan Odoo pada source database yang sama.

## Verifikasi

- Controller mapping dan Blade berhasil dicache/divalidasi.
- Mapping existing tetap dibaca dari `core_external_references`.
- Proses sync tidak membuat atau mengubah master `core_companies` secara otomatis.
