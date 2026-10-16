# Log pekerjaan — Migrasi fitur legacy ke Workspace

## Temuan awal

- Runtime target arsitektur adalah `app/workspace` pada `http://localhost:8080`.
- File Organizer dan Project Management masih ada sebagai source legacy dan
  sempat dapat mengambil port runtime secara tidak sengaja.
- Workspace sudah memiliki Document Management dan Project Management yang
  dipertahankan dalam satu panel dan satu runtime.
- Fitur SSO/OIDC legacy sengaja tidak dimigrasikan karena runtime Workspace
  menggunakan login lokal sesuai ADR-013.

## Status

- Requirement dan acceptance criteria migrasi ditambahkan.
- Checklist diturunkan dari selesai menjadi berjalan sampai parity terverifikasi.
- Menambahkan domain Workspace untuk Epic, Project Notes, Ticket Comments, dan
  Notification serta migration idempotent pada koneksi `pm`/`core`.
- Menambahkan resource Filament Epic dan Project Notes serta relation manager
  komentar pada detail Ticket.
- Menyamakan group navigasi resource dokumen menjadi `Document Management`.
- Parity fitur yang dipertahankan sudah diimplementasikan; verifikasi akhir
  dilakukan sebelum penghapusan folder legacy.

## Risiko

- Migration database harus dipastikan hanya dimiliki Workspace sebelum source
  legacy dihapus.

## Verifikasi akhir

- Resource/page/model/service Document Management dan Project Management yang
  dipertahankan tersedia di Workspace, termasuk board, timeline, epic, notes,
  comments, status/priority, widgets, relation managers, dan ticket template
  import/export.
- Database test berhasil melalui `migrate:fresh --seed`; prefix modul final
  (`core_`, `crm_`, `pm_`, `doc_`) direkonsiliasi secara idempotent.
- `php artisan test`: 26 test, 75 assertion lulus.
- `php artisan route:list --except-vendor` dan `npm run build` berhasil.
- SSO/OIDC, Google login, dan external portal tidak dipindahkan karena
  ADR-013; authentication runtime Workspace tetap login lokal.
- `app/file-organizer` dan `app/project-management` dihapus sebagai source
  runtime legacy setelah verifikasi ini. `app/project-sso` tetap di luar scope.
