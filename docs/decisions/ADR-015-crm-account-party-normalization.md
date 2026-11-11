# ADR-015 — Normalisasi account CRM dan party Odoo

Status: accepted — 2026-11-09

## Keputusan

- `core_companies` menjadi master perusahaan lintas peran: internal, client,
  partner, dan vendor.
- `crm_accounts` adalah profil hubungan CRM dan dapat bertipe `company` atau
  `person`.
- Account perusahaan menunjuk ke `party_company_id`; account perorangan tidak
  membuat record `core_companies`.
- Status account nullable bila sumber belum memberikan kepastian. Stage dan
  closing status tetap berada pada opportunity.
- External reference Odoo menjadi kunci upsert. ID Odoo tidak pernah dipakai
  sebagai primary key Workspace.
- Partner yang merupakan `res_company.partner_id` mengikuti mapping
  `res_company` agar tidak dibuat sebagai perusahaan baru.

## Konsekuensi

Account tanpa perusahaan operasional belum terlihat oleh user non-super-admin
yang memakai scope perusahaan internal sampai ownership-nya ditentukan.
