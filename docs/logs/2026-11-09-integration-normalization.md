# Log 2026-11-09 — Normalisasi Odoo ke Workspace

## Perubahan

- Menambahkan normalisasi runtime setelah projection Odoo berhasil di-upsert.
- `crm.lead` Odoo dinormalisasi ke satu tabel pipeline CRM Workspace, yaitu `crm_opportunities`, dengan `record_type` `lead` atau `opportunity`.
- Project Odoo dinormalisasi ke `core_projects` menggunakan mapping `res_company` sebagai penentu `company_id`.
- Relasi external-to-local disimpan di `core_external_references` dengan `local_entity_type` `crm_pipeline_item` atau `project`.
- Opportunity Odoo yang memiliki project akan dihubungkan ke project lokal setelah project berhasil dinormalisasi.
- Mapping perusahaan dipertahankan saat sync berikutnya; status `mapped` tidak lagi tertimpa menjadi `synced`.

## Verifikasi lokal

- Migration terpadu pipeline berhasil dijalankan.
- Full sync berhasil: 172 record diproses, 0 gagal.
- Hasil normalisasi: 62 pipeline opportunity, 5 project lokal, dan 42 referensi partner menjadi account CRM.
- 1 perusahaan Odoo belum dimapping, sehingga project miliknya belum dinormalisasi.

## Catatan

- Data hasil normalisasi dibuat saat sync runtime melalui upsert, bukan melalui migration atau seeder.
- Tabel `crm_leads` lama dipertahankan untuk kompatibilitas dan belum dihapus.
