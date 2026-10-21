# 2026-10-16 — Keputusan penghapusan Filament dan boundary modul

## Perubahan

- Menetapkan Laravel Blade + Bootstrap sebagai fondasi UI.
- Menetapkan Livewire hanya untuk komponen yang membutuhkan interaksi dinamis.
- Menetapkan Filament tidak boleh menjadi dependency, provider, route, view component,
  atau namespace runtime.
- Mendokumentasikan Core sebagai platform bersama dan empat modul bisnis aktif:
  Company, CRM, Project Management, dan Document Management.
- Menambahkan boundary `app/Modules/` dan placeholder HRIS.
- Mempertahankan satu aplikasi, satu login/session, satu database, Redis, MinIO,
  dan satu deployment.

## Verifikasi

- Dokumentasi arsitektur, requirement runtime, ADR, checklist, dan README workspace
  telah diselaraskan.
- Dependency Filament, Filament Shield, dan plugin layout Filament telah dihapus
  dari Composer Workspace dan core.
- Provider, resource/page/widget, view, asset, middleware, serta konfigurasi Filament
  lama telah dibersihkan dari runtime.
- Login Laravel, dashboard Workspace, route module launcher, layout Blade, dan asset
  Bootstrap telah disiapkan sebagai baseline migrasi.
- `composer validate`, `php artisan package:discover`, route discovery, Blade cache,
  route cache, dan `npm run build` berhasil.
- Namespace dan referensi runtime telah dipindahkan ke boundary modul; policy didaftarkan
  eksplisit melalui `Gate::policy` agar discovery tidak bergantung pada struktur namespace.
- Folder legacy `app/Domain`, `app/Policies`, dan `app/Services` yang kosong/tidak terpakai
  telah dihapus. `app/Filament` sebelumnya sudah tidak ada.
- Card modul tidak lagi kembali ke dashboard umum: dashboard Blade untuk Company, CRM,
  Project Management, dan Document Management telah ditambahkan dengan statistik dan
  tabel data terbaru.
- Shell navigasi Blade telah dikembalikan dengan sidebar per modul, dropdown perpindahan
  modul, responsive offcanvas pada mobile, dan menu awal sesuai domain masing-masing.
- Menu Document Management telah dihubungkan ke halaman nyata untuk daftar dokumen dengan
  filter, tipe dokumen, verifikasi, dan bundel; tidak lagi menggunakan anchor placeholder.
- CRUD perusahaan telah ditambahkan pada modul Company: list, search, detail, create, edit,
  soft delete, policy authorization, dan validasi Form Request boundary controller.
- CRUD dasar CRM telah ditambahkan untuk Account, Lead, Opportunity, dan Activity dengan
  controller resource bersama, policy authorization, pencarian, pagination, form, dan validasi.
- CRUD dasar Project Management telah ditambahkan untuk Project, Ticket, Epic, Catatan,
  Status, dan Prioritas dengan route resource, form, pagination, validasi, serta policy
  Project/Ticket.
- Core administration telah memiliki halaman pengguna, form pembuatan user dengan password
  confirmation, serta daftar role dan jumlah pengguna berdasarkan policy lokal.
- CRUD Vendor telah ditambahkan dengan company scope, policy, validasi, pagination, form,
  edit, dan soft delete.
- Form upload Document Management telah ditambahkan menggunakan DocumentUploadService,
  validasi tipe/ukuran file, metadata dasar, dan private object storage.
- Workflow verifikasi telah ditambahkan: detail kasus, review item passed/failed/waived,
  komentar reviewer, serta release/reject dokumen melalui service lifecycle dan policy.
- Request bundel telah ditambahkan dengan company scope wajib, filter pencarian/status/tanggal,
  dispatch `GenerateDocumentBundle`, status queue, dan halaman hasil/download.
- User administration telah diperluas dengan edit, update, assignment role, dan archive;
  akun sistem tetap dilindungi oleh policy dan model guard.
- CRUD dokumen diperluas dengan edit/update metadata dari UI; file dan versioning tetap
  dipisahkan agar perubahan metadata tidak menimpa object storage.
- Role administration telah diperluas dengan create, edit, update, delete terproteksi,
  unique validation, dan perlindungan khusus role `super_admin`.
- Halaman board ticket dan timeline Project Management telah ditambahkan menggunakan query
  scoped model dan tampilan Bootstrap; menu sidebar tidak lagi mengarah ke anchor placeholder.
- Audit lanjutan memperbaiki deklarasi method board/timeline yang sempat berada di luar class;
  seluruh 106 file PHP aplikasi, view, dan route lolos `php -l`. `npm run build` dan
  `composer validate` juga berhasil.
- Pemulihan dependency development Composer kembali terhambat oleh Windows Search/antivirus
  saat ekstraksi paket PHPUnit/Pest; `php artisan test` belum tersedia sampai vendor selesai
  dipasang.
- `php -l` berhasil untuk 83 file PHP aplikasi, database, route, dan test.
- `php artisan test` masih terblokir karena Windows Search/antivirus mengunci proses
  ekstraksi `phpunit/phpunit`; file PHPUnit sudah diekstrak sebagian tetapi Composer
  belum menyelesaikan registrasi package.

## Risiko tersisa

### Audit parity lanjutan

- CRUD CRM diperbaiki agar field wajib `company_id`, relasi account/opportunity, dan field activity `subject` sesuai model; Contact juga tersedia sebagai resource.
- Project Management ditambah CRUD komentar ticket, notifikasi, kontribusi pengguna, dan leaderboard.
- Document Type ditambah CRUD, validasi metadata JSON, policy super admin, serta pencegahan delete saat masih direferensikan dokumen.
- Project Management ditambah halaman detail project dengan daftar, tambah, dan hapus anggota serta ringkasan ticket/epic/catatan.
- Ticket dilengkapi field status, prioritas, assignee multi-pilih, validasi, dan sinkronisasi pivot `pm_ticket_users`.
- Form upload/edit dokumen memakai Livewire terbatas untuk memilih tipe dan merender metadata dinamis; aturan bisnis tetap berada di `DocumentUploadService`.
- Detail Account CRM dan Project Management kini menampilkan relation data dokumen serta shortcut upload kontekstual.
- Action import/export ticket dari resource lama dipindahkan menjadi endpoint CSV pada boundary Project Management dengan validasi dan authorization per project.
- Verifikasi terbaru: 76 file PHP lolos `php -l`, `npm run build`, dan `composer validate --no-check-publish` berhasil.
- `php artisan optimize:clear`/cache masih tidak dapat dijalankan pada environment ini karena `Laravel\\Pail\\PailServiceProvider` belum tersedia akibat Composer dev dependency belum selesai diekstrak.
- Gap yang tersisa: UI dedicated untuk assignee ticket dan relation manager dokumen serta komponen Livewire dinamis untuk metadata/filter.

- UI fitur bisnis existing perlu dibangun ulang bertahap pada boundary modul;
  domain model, policy, service, job, storage, dan audit dipertahankan.
- Test suite belum dapat dijalankan penuh karena Windows Search/antivirus mengunci
  beberapa direktori paket development saat Composer memasang ulang vendor.
- UI fitur bisnis existing perlu dibangun ulang bertahap pada boundary modul; baseline
  login, dashboard, dan launcher sudah tersedia dengan Blade + Bootstrap.
- CRUD lengkap, filter, form, board, timeline, dan workflow interaktif masih menjadi
  pekerjaan lanjutan per modul.

### Perbaikan form tipe dokumen

- Form bersama create/edit tipe dokumen dinormalisasi agar mode create tidak membaca
  properti dari model `null` (`name`, `code`, `company_id`, `required_fields`, dan
  `is_active`).
- Ditambahkan regression test `DocumentTypeFormViewTest` untuk memastikan route
  `/workspace/documents/types/create` menghasilkan halaman tanpa error.
- Verifikasi: test targeted lulus, `php artisan view:cache`, `php artisan route:cache`,
  dan pemeriksaan sintaks controller berhasil.
