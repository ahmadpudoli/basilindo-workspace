# Requirement CRM Minimum dan Pipeline

Status: implementasi awal.

## Tujuan

Menyediakan CRM minimum yang dapat mengelola pipeline Sales seperti Odoo tanpa
menduplikasi master `Company`, serta menyediakan konteks client, Sales/PIC,
status komersial, dan project untuk kebutuhan HRIS external/project.

## Ruang lingkup

- `Company` tetap menjadi master perusahaan.
- `core_companies` tetap menjadi master perusahaan untuk internal, client, partner, dan vendor.
- `crm_accounts` menjadi profil hubungan CRM; account dapat bertipe `company` atau `person`.
- Account perusahaan dapat menunjuk ke `crm_accounts.party_company_id`; account perorangan tidak membuat record `core_companies`.
- Status account boleh `NULL` sampai ada sumber atau aturan bisnis yang menetapkannya. Status pipeline tetap berada di opportunity.
- Contact menyimpan individu dan dapat berdiri sendiri untuk prospek perorangan.
- Lead dapat dikonversi menjadi Opportunity.
- Opportunity memakai stage configurable dan closing status terpisah.
- Pipeline menampilkan card yang dapat dipindahkan antar-stage secara terotorisasi.
- Lead dan opportunity ditampilkan sebagai satu pipeline sales; tipe item tetap terlihat pada card.
- Dashboard dan navigasi CRM memakai satu entry point Pipeline Sales, bukan menu Lead dan Opportunity yang terpisah.
- Status account tidak menjadi bagian dari UI account sampai definisi dan sumber datanya disepakati; kolom, filter, dan form account tidak menampilkannya.
- Konfigurasi CRM berada pada menu terpisah dengan submenu Stage Pipeline dan Team
  Sales; setiap submenu memakai permission granular.
- Opportunity dapat dikaitkan ke satu project.
- Pipeline Odoo yang tidak lagi aktif pada full sync diarsipkan secara soft delete dengan status sumber `deleted`, bukan dihapus permanen.
- Pipeline yang muncul kembali dari Odoo memulihkan record lokal yang sama berdasarkan external reference.
- Product, quotation, komisi, forecasting, dan otomasi Sales berada di luar fase ini.

## Field minimum

### Pipeline stage

Stage memiliki `code`, `name`, `sequence`, `is_won`, `is_delivery`,
`is_folded`, `requires_expected_close`, dan `is_active`.

### Closing status

Status memiliki `code`, `name`, `is_lost`, `is_ignored`, `is_closed`, dan
`is_active`. Alasan wajib diisi untuk status lost, ignored, atau cancelled.

### Opportunity

Opportunity menyimpan account, contact opsional, nama, stage, closing status,
Sales/PIC, nilai, mata uang, estimasi closing, project opsional, referensi PO,
waktu won/closed, dan external reference.

## Acceptance criteria

- Admin dapat mengatur stage, urutan, stage Won, stage Delivery, dan folded state.
- User hanya melihat submenu konfigurasi jika memiliki `manage_crm_pipeline` atau
  `manage_crm_team` yang sesuai.
- Team Sales dapat dibuat dan dipilih pada opportunity.
- Pipeline menampilkan opportunity dalam kolom sesuai urutan stage.
- Pipeline menampilkan label tipe Prospek atau Opportunity pada setiap card.
- User berizin dapat memindahkan card antar-stage melalui drag-and-drop.
- Perubahan stage divalidasi server-side dan dicatat audit.
- Stage Won dapat ditandai sebagai sumber pembuatan project.
- Closing status mendukung Open, Closed, Ignored, Lost, dan Cancelled.
- Lost/Ignored/Cancelled membutuhkan alasan.
- Opportunity dapat memiliki project dan client dari Company yang sudah ada.
- User dapat menampilkan item yang dihapus di Odoo dan melihat penanda `Dihapus di Odoo` pada pipeline.
- HRIS dapat membaca project aktif beserta client, Sales/PIC, opportunity, status
  komersial, dan referensi PO.
- Migration bersifat incremental; kolom lama `stage` tetap kompatibel selama
  backfill dan transisi.
