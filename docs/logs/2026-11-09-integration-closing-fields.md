# Log 2026-11-09 — Closing status dan field CRM Odoo

## Perubahan

- `status` pipeline tidak lagi default `open` saat normalisasi Odoo.
- `status` dan `closing_status_id` menjadi `lost` atau `closed` jika Odoo memiliki indikator closing; selain itu nilainya `null`.
- `lost_reason` mengambil nama dari `crm_lost_reason`.
- `won_at` dan `closed_at` mengambil `date_closed` jika tersedia.
- `team_id` dipetakan ke team CRM Workspace melalui external reference `crm_team`.
- `po_number` dan `po_document_id` tidak diisi karena belum tersedia pada sumber `crm.lead`; nilai lokal yang sudah ada dipertahankan saat sync.
- Duplicate pipeline item akibat sync awal sebelum external reference tersimpan ditandai soft delete dan ditambahkan unique index Odoo.

## Verifikasi

- Full sync berhasil: 172 diproses, 0 gagal.
- Active pipeline: 62 record.
- Status active pipeline: 50 `closed`, 12 `null`.
- Duplicate orphan: 62 record ditandai soft delete.
- Error pada sync terakhir: 0.
