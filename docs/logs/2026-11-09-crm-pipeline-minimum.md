# 2026-11-09 — CRM minimum dan pipeline Sales

## Perubahan

- Menambahkan migration incremental untuk `crm_pipeline_stages` dan
  `crm_closing_statuses`.
- Menambahkan relasi stage, closing status, contact, alasan kalah, waktu won/closed,
  nomor PO, dan external reference pada opportunity.
- Menambahkan jejak konversi lead ke opportunity.
- Menambahkan halaman pipeline dengan card drag-and-drop dan endpoint server-side
  untuk perpindahan stage.
- Menambahkan halaman konfigurasi stage dan status closing.
- Mempertahankan tabel CRM yang ada dan kolom legacy `stage` selama masa transisi.
- Menambahkan link pipeline dari dashboard CRM dan dukungan form opportunity untuk
  stage, closing status, serta nomor PO.
- Menambahkan menu `Pipeline Sales` pada sidebar CRM agar fitur tidak tersembunyi
  di balik URL manual.
- Mengubah pipeline menjadi area lebar penuh dengan toolbar dan dialog modal
  untuk membuat opportunity baru tanpa berpindah halaman.
- Menambahkan requirement CRM minimum dan acceptance criteria.

## Verifikasi

- Migration dijalankan pada database development Workspace dan membuat stage/status
  baseline: `new`, `qualified`, `technical_review`, `proposal`, `negotiation`,
  `won`, `delivery`, serta `open`, `closed`, `ignored`, `lost`, `cancelled`.
- `php artisan route:list --path=workspace/crm` berhasil menampilkan route pipeline.
- PHP lint untuk migration, model, controller berhasil.
- Blade cache berhasil dibuat.
- `php artisan test --filter=HrisWorkflowTest`: 12 test, 35 assertion lulus.
- `npm.cmd run build`: berhasil.

## Risiko tersisa

- Integrasi sinkronisasi dengan Odoo belum diimplementasikan; field `external_source`
  dan `external_id` baru disiapkan.
- Drag-and-drop saat ini mengubah stage, sedangkan perubahan Lost/Ignored dengan
  alasan masih perlu dilakukan melalui alur form/status lanjutan.
- Product, quotation, Sales team, dan reporting pipeline belum termasuk fase ini.

## Penyesuaian UI konfigurasi

- Konfigurasi dipindahkan dari toolbar pipeline ke parent menu `Konfigurasi`.
- Ditambahkan submenu `Stage Pipeline` dan `Team Sales` dengan permission
  `manage_crm_pipeline` dan `manage_crm_team`.
- Ditambahkan migration dan resource minimum Team Sales, termasuk pilihan team
  pada dialog pembuatan opportunity.
- Memperbaiki layout kontrol pencarian pipeline agar ikon, input, dan tombol tetap
  berada dalam satu baris.
- Memberikan `manage_crm_pipeline` dan `manage_crm_team` kepada user
  `ahmad.pudoli@legospay.id` pada database Workspace development.
