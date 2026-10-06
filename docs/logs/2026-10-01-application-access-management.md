# 2026-10-01 — Pengelolaan Application Access

## Perubahan

- Menambahkan halaman edit dan aksi baris edit, hapus, serta aktif/nonaktif pada
  `project-sso/admin/application-accesses`.
- Menambahkan policy application access dan permission Filament Shield untuk
  view/create/update/delete.
- Menambahkan surrogate `id` melalui migration reversible, sambil mempertahankan
  unique constraint user–application–role.
- Menambahkan audit log metadata untuk create, update, perubahan status, dan delete.
- Menambahkan requirement, acceptance criteria, test model, dan ADR.

## Verifikasi

- `php -l` seluruh file yang berubah: lulus.
- `php artisan migrate --pretend`: lulus.
- Migration diterapkan ke database development lokal: lulus.
- `php artisan test --compact`: 18 test, 40 assertion lulus.
- Route tersedia untuk index, create, dan edit application access.

## Risiko tersisa

- Audit saat ini masuk ke channel log aplikasi; durable audit event table untuk SSO
  masih menjadi pekerjaan security/observability terpisah.
