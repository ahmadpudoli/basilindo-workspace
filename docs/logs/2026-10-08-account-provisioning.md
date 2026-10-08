# Log pekerjaan — Provisioning akun dan akses aplikasi

## Perubahan

- Menonaktifkan halaman create-user lokal pada File Organizer dan Project Management.
- Menghapus field identitas user dari form edit aplikasi client; identitas dikelola oleh Project SSO.
- Menambahkan requirement dan acceptance criteria provisioning akun lintas aplikasi.
- Menyamakan Project Management dengan File Organizer pada assignment perusahaan di halaman edit user.
- Menambahkan launcher aplikasi pada topbar Project Management berdasarkan daftar aplikasi dari claim SSO.
- Menyamakan perlindungan akun sistem pada aksi edit/delete user Project Management.
- Mengarahkan user yang sudah login dari root File Organizer dan Project Management langsung ke `/admin`.
- Menetapkan arah evolusi `app/file-organizer` menjadi Basilindo Workspace modular monolith dengan lima boundary domain.
- Memulai CRM MVP dengan schema account, contact, lead, opportunity, dan activity.
- Menambahkan resource Account dengan filter company scope dan policy authorization.
- Menambahkan pengelolaan Contact person pada halaman detail/edit Account.
- Menambahkan resource Lead dan Opportunity dengan filter/option berbasis company scope.
- Menambahkan Activity resource dengan due date/completion state.
- Menambahkan service konversi Opportunity menjadi Project beserta policy dan assignment owner.
- Menambahkan Activity resource dengan due date, assignment, dan completion state.
- Menambahkan Project Management Workspace: ticket/task, status, priority, assignee, dan project scope.
- Memindahkan navigasi Project ke grup Project Management dan membatasi pilihan company saat membuat project.
- Menambahkan ringkasan lintas modul pada dashboard Workspace dengan guard tabel untuk masa transisi migration.
- Menghubungkan dokumen dengan CRM Account dan Opportunity melalui reference ID serta form/filter metadata dokumen.
- Menambahkan relation manager ticket dan dokumen pada detail project, termasuk shortcut membuat ticket dan upload dokumen.
- Menambahkan Account detail dengan contact, opportunity, activity, dan dokumen terkait beserta shortcut pembuatan data.
- Menambahkan Lead policy dan memperketat option Opportunity pada Activity berdasarkan company scope.
- Memperbaiki referensi model lintas boundary agar master Company, Project, dan User selalu menggunakan `Core\\Models` pada Workspace.
- Memperbaiki query scope yang masih memakai alias `companies.id` menjadi `core_companies.id` sesuai prefix koneksi Core.
- Memperketat pilihan perusahaan, project, CRM Account, dan CRM Opportunity pada form dokumen berdasarkan akses user.
- Menambahkan ringkasan pipeline opportunity per stage pada dashboard Workspace.
- Menambahkan role matrix awal `finance_*`, `document_admin`, `crm_*`, dan `project_*` pada RoleSeeder.
- Memperbaiki test isolation dan referensi model test agar koneksi Core dan Workspace tervalidasi sesuai boundary.
- Menambahkan kontrak projection application access SSO yang aman dan projection lokal `core_application_access`.
- Menambahkan command `sso:sync-application-access` dan filter User File Organizer berdasarkan access aktif.
- Menjadwalkan sinkronisasi projection setiap jam dengan guard environment dan lock scheduler.
- Menambahkan safe empty state pada halaman Users ketika tabel projection belum tersedia atau belum memiliki access aktif.
- Menambahkan retry policy, failed-state handling, dan idempotensi pada job bundling/cleanup.
- Menambahkan runbook backup/restore, monitoring, release checklist, dan rollback plan.
- Menambahkan GitHub Actions CI untuk PostgreSQL, migration dry-run, test suite, dan frontend build.
- Menyelaraskan checklist bahwa lima boundary modular monolith sudah memiliki baseline implementasi di `app/file-organizer` dan `core/`.
- Menghapus logging dan file dump kredensial dari migration legacy SSO; log kini hanya mencatat jumlah record yang dibuat.
- Mengaktifkan validasi metadata wajib berdasarkan Document Type, normalisasi reference/tag, upload ke status quarantine, lifecycle release/reject, dan legal hold service.
- Mengaktifkan kembali tabel serta resource Filament `SavedSearch` setelah tabel legacy sebelumnya dipensiunkan tanpa consumer.
- Verifikasi batch: 5 test terarah, 14 assertion lulus; route Filament saved search berhasil ditemukan.
- Menambahkan policy review dokumen, audit event login SSO dan perubahan role, halaman detail/edit dokumen, serta action release/reject berbasis role.
- Verifikasi regresi penuh setelah batch authorization/UI: 27 test, 71 assertion lulus.
- Menambahkan preview inline terotorisasi untuk PDF/JPEG/PNG dengan `private, no-store`, `nosniff`, dan audit event; tipe file lain tetap ditolak.
- Verifikasi preview/download: 3 test, 9 assertion lulus.
- Verifikasi final seluruh suite File Organizer: 28 test, 76 assertion lulus.
- Menambahkan `GET /health/ready` untuk readiness database, queue/cache Redis, failed jobs, dan private object storage tanpa menulis object probe.
- Menambahkan requirement, runbook monitoring, serta test healthy/failure path readiness: 2 test, 16 assertion lulus.
- Verifikasi final seluruh suite setelah monitoring: 30 test, 92 assertion lulus.
- Audit completion MVP: lima boundary modular, SSO/OIDC, UI/UX File Organizer, dokumentasi, readiness monitoring, dan verifikasi telah memiliki evidence; OCR tetap dicadangkan sebagai fase lanjutan.
- Diagnosis CRM navigation: resource dan route CRM sudah terdaftar, tetapi navigation mengikuti policy company scope. Menambahkan mapping SSO untuk role CRM/Project; assignment company tetap wajib dan tidak dilewati.
- Menambahkan halaman hub CRM dengan shortcut Account/Lead/Opportunity/Activity dan empty state assignment company tanpa membuka data lintas scope.
- Menambahkan data migration additive-only untuk role/permission CRM dan Project pada existing install; tidak mencabut permission atau membuat company membership otomatis.
- Verifikasi regresi setelah role mapping dan CRM hub: 31 test, 94 assertion lulus.

## Verifikasi

- Route create-user tidak lagi didaftarkan pada resource User client.
- Project SSO tetap menjadi tempat pembuatan user dan pengelolaan application access.
- PHP lint berhasil untuk resource user, halaman edit, relation manager perusahaan, dan provider Project Management.
- PHP lint, route discovery, dan `migrate --pretend` berhasil untuk CRM Account.
- PHP lint seluruh source `app/` dan route discovery CRM, Project Management, serta Document Management berhasil.
- `migrate --pretend` berhasil untuk migration CRM, Workspace Project Management, dan relasi CRM pada dokumen.
- Percobaan awal `php artisan test` dari sandbox tidak dapat menulis cache/view/log Laravel; verifikasi berikutnya dijalankan dengan izin runtime yang sesuai.
- `php artisan test` berhasil: 22 test dan 57 assertion lulus setelah dijalankan dengan izin penulisan runtime.
- `npm.cmd run build` berhasil: Vite mentransformasi 59 module dan menghasilkan asset production.
- Test Project SSO berhasil: 4 test dan 27 assertion lulus, termasuk endpoint projection dengan client credentials.
- Test File Organizer setelah projection access berhasil: 22 test dan 57 assertion lulus.
- Test File Organizer setelah retry/idempotency job: 23 test dan 60 assertion lulus.
- Compose ketiga aplikasi divalidasi; nama container, host port dependency, dan health check MinIO diisolasi agar stack dapat berjalan bersamaan.
- `php artisan optimize:clear` berhasil; `view:cache` belum dapat menyelesaikan kompilasi karena permission `storage/logs/laravel.log` dan `storage/framework/views` pada environment Windows.

## Pekerjaan lanjutan

- Menyediakan provisioning client credentials pada deployment setiap environment.
- Menjalankan sinkronisasi projection secara scheduler/cron setelah migration core aktif.

## Risiko

- Selama scheduler sync belum diaktifkan, admin perlu menjalankan command projection secara manual.
- Verifikasi stack Docker Compose penuh dan hardening release masih menjadi fase berikutnya.

## Perubahan arsitektur single-app, 2026-10-08

- Keputusan terbaru: `app/file-organizer` menjadi satu-satunya aplikasi aktif Basilindo Workspace.
- Login lokal `/admin/login` menjadi default; `project-sso` tidak diperlukan untuk operasi normal dan hanya dipertahankan sebagai adapter OIDC opsional.
- Resource Users kini menyediakan pembuatan user Core, dan daftar user membaca seluruh user Workspace tanpa filter application access lama.
- Role dan company membership tetap menjadi kontrol authorization lintas modul.
- Dokumentasi panduan provisioning dan requirement diperbarui agar tidak lagi mengarahkan admin ke SSO terpisah.
