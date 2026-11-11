# Pemisahan scheduler ke console routes

## Perubahan

- Memindahkan definisi scheduler dari workspace/bootstrap/app.php ke workspace/routes/console.php.
- Mendaftarkan command cleanup temporary upload secara eksplisit melalui withCommands.
- Memperbaiki filter incremental Odoo agar memakai qualifier tabel yang benar:
  res_company.write_date untuk company dan res_partner.write_date untuk partner.
- Mengubah command integration:sync-odoo agar hanya dispatch RunOdooReferenceSync.
- Menambahkan unique lock terpisah untuk sync incremental dan full.
- Menetapkan queue domain eksplisit: odoo-sync, document-bundles, document-cleanup, dan notifications.
- Memperbarui worker development agar memproses seluruh queue domain tersebut.
- Menaikkan retry worker dan job Odoo menjadi 3 percobaan dengan backoff agar kegagalan sementara tidak langsung menjadi failed job.
- Memindahkan infrastruktur Web Push ke Core Notifications: job, model subscription, dan controller endpoint.
- Memperbarui HRIS agar hanya menjadi pemicu notifikasi melalui job Core.

Verifikasi tambahan:

- PHP lint untuk job, model, controller Core, dan HrisWorkflowService
- php artisan optimize:clear
- Tidak ada referensi namespace Web Push lama di HRIS atau routes

## Verifikasi normalisasi

- Ditemukan 62 reference CRM lead dengan local_id stale yang menunjuk ke opportunity yang sudah tidak ada.
- Normalizer diperbaiki agar memeriksa keberadaan row lokal sebelum update dan membuat ulang row jika reference stale.
- Full sync melalui scheduler/queue berhasil selesai dengan 172 diproses, 0 gagal.
- Setelah full sync, tabel crm_opportunities berisi 62 row hasil normalisasi.

## Verifikasi

- php -l bootstrap/app.php
- php -l routes/console.php
- php -l app/Modules/Integration/Application/Services/OdooPostgresDataSource.php
- php artisan optimize:clear
- php artisan schedule:list
- php artisan list
- php artisan integration:sync-odoo -vvv (berhasil, exit code 0)
- Test command dispatch untuk mode incremental dan full

Catatan: test feature dispatch belum dapat berjalan karena database test belum memiliki
tabel ws_migrations dan migrasi multi-koneksi test mengalami konflik tabel core_users.
Validasi manual command dispatch berhasil dan menghasilkan exit code 0.

## Risiko tersisa

- Sinkronisasi Odoo tetap bergantung pada ketersediaan database Odoo dan konsistensi data projection CRM.
