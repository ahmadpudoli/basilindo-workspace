# Normalisasi account dan partner Odoo

## Perubahan

- Menambahkan `account_type` dan `party_company_id` pada `crm_accounts` secara incremental.
- `crm_accounts.status` menjadi nullable; hasil Odoo tidak lagi diberi default `prospect`.
- Partner Odoo bertipe perusahaan dipetakan ke `core_companies` dengan role `client` dan `partner`.
- Partner perorangan tanpa parent menjadi account tipe `person` tanpa membuat `core_companies`.
- Partner dengan parent menjadi contact dari account parent.
- Upsert memakai `core_external_references`; ID Odoo tidak pernah dipakai sebagai ID lokal.
- Partner yang merupakan `res_company.partner_id` mengikuti mapping perusahaan Odoo yang sudah ada.
- Konflik account perusahaan menggunakan account canonical; data lama tidak dihapus permanen.

## Verifikasi

- Migration `2026_11_09_000008_normalize_crm_accounts` berhasil dijalankan.
- Full sync Odoo terakhir: 172 diproses, 0 gagal.
- Database menghasilkan 90 account aktif: 51 perusahaan, 19 perorangan, seluruh status kosong.

## Risiko tersisa

- Account yang tidak memiliki `company_id` operasional belum dapat terlihat oleh user non-super-admin yang memakai scope perusahaan internal.
- Perusahaan dengan nama Odoo ambigu tetap memerlukan mapping manual.
