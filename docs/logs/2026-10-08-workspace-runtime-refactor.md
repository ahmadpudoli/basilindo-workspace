# Log Refactor Workspace Runtime — 2026-10-08

## Perubahan

- Membuat `app/workspace` sebagai kandidat runtime Laravel Filament tunggal.
- Menggabungkan lima boundary domain: Core, Company, CRM, Project Management,
  dan Document Management.
- Menetapkan login lokal Workspace sebagai satu-satunya authentication runtime.
- Menghapus route SSO/Google, command projection SSO, scheduler SSO, konfigurasi
  SSO, controller SSO, dan test SSO dari kandidat Workspace.
- Menetapkan URL development `http://localhost:8080`, prefix tabel Workspace
  `ws_`, dan `APP_MIGRATION_SET=workspace`.
- Menambahkan workflow CI `workspace-ci.yml`, requirement runtime tunggal, dan
  ADR-013.

## Verifikasi

- Composer dependency berhasil dipasang pada `app/workspace`.
- Route `/admin/login` dan route CRM terdaftar pada kandidat Workspace.
- Test suite Workspace: 25 test dan 73 assertion lulus menggunakan database
  terisolasi `db_workspace_test`.
- `npm ci` dan `npm run build` lulus; Vite menghasilkan asset production 59 module.

## Pekerjaan lanjutan

- Migration fresh pada database Workspace baru dan test suite sudah diverifikasi.
- Migrasikan/arsipkan `app/file-organizer` serta `app/project-management` setelah
  smoke test Workspace dan build frontend lulus.

## Perbaikan migration permission Workspace

- Memperbaiki migration permission agar nama index dan primary key mengikuti
  prefix aktif (`ws_`), bukan nama legacy `fo_`.
- Memperbaiki constraint domain `document_relation_unique` dan
  `document_parties_unique` agar tidak bentrok dengan constraint legacy.
- Membuat migration projection Core idempotent ketika tabel `application_access`
  sudah tersedia pada database Core.
- Menjalankan `php artisan migrate --force` pada database Workspace existing
  dengan `CORE_MIGRATIONS_OWNER=false`; seluruh migration pending berhasil.
- Tabel permission Workspace yang terverifikasi: `ws_roles`, `ws_permissions`,
  `ws_model_has_roles`, `ws_model_has_permissions`, dan
  `ws_role_has_permissions`.
- Admin lokal `admin@example.com` dipulihkan aksesnya dengan role `super_admin`
  melalui seeder; validasi akses panel tidak lagi gagal karena role kosong.
- Target prefix per modul dicatat pada ADR-014 dan sudah diterapkan pada
  database Workspace.
- Migration cleanup kemudian dijalankan setelah konfirmasi eksplisit:
  `core_*`, `crm_*`, `pm_*`, dan `doc_*` sekarang menjadi namespace aktif;
  `ws_*` tersisa untuk runtime teknis. Tabel legacy berisi dipindahkan ke
  schema `legacy`, sedangkan tabel legacy kosong dibersihkan.
- Model CRM, Project Management, Document Management, Role, dan Permission
  sudah diarahkan ke koneksi/prefix modul masing-masing.
- URL login Workspace diubah dari `/admin/login` menjadi `/login`; dashboard
  utama berada di `/`.
- Dashboard `/` diubah menjadi launcher aplikasi. Resource sidebar sekarang
  mengikuti modul yang dipilih pada session; route `/workspace` mengembalikan
  pengguna ke launcher dan membersihkan pilihan modul.
