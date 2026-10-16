# Requirement: Migrasi fitur legacy ke Workspace

## Tujuan

Memindahkan seluruh fitur bisnis yang masih diperlukan dari `app/file-organizer`
dan `app/project-management` ke `app/workspace` sebagai satu aplikasi Laravel
Filament modular monolith.

## Batasan

- `app/workspace` menjadi satu-satunya runtime.
- Fitur bisnis Document Management dari File Organizer harus tersedia pada
  modul `Document Management`.
- Fitur bisnis Project Management dari Project Management harus tersedia pada
  modul `Project Management`.
- SSO/OIDC, route aplikasi terpisah, session lintas aplikasi, dan konfigurasi
  port legacy tidak dipindahkan karena bertentangan dengan ADR-013.
- Folder legacy hanya boleh dihapus setelah seluruh acceptance criteria,
  test, build, dan smoke test Workspace lulus.

## Acceptance criteria

- Semua resource, page, relation manager, policy, service, job, command, dan
  migration bisnis yang dipertahankan tercatat pada matriks parity.
- Document Management memiliki fitur dokumen, tipe dokumen, metadata, saved
  search, upload, preview/download, lifecycle, verification, relations,
  bundling, audit, dan akses dari project/account.
- Form dokumen wajib memilih perusahaan internal aktif dalam scope user dan
  perusahaan client/vendor eksternal; relasi CRM lama dipertahankan untuk
  kompatibilitas data tetapi tidak ditampilkan sebagai input utama.
- Project Management memiliki project, member, ticket, status, priority,
  assignee, epic, notes, comments, board, timeline, dashboard/report,
  notification, policy, dan relation manager yang masih relevan.
- Semua modul berjalan pada satu login lokal, satu session, satu database,
  satu queue, dan satu deployment Workspace.
- Test happy path dan failure path untuk fitur yang dipindahkan lulus.
- `php artisan test`, `php artisan migrate:fresh --seed`, `npm run build`, dan
  smoke test HTTP Workspace lulus.
- Tidak ada referensi runtime ke `app/file-organizer`,
  `app/project-management`, atau `app/project-sso`.
- Folder legacy baru dihapus setelah verifikasi akhir dan dicatat pada log.
