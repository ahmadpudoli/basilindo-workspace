# Pemisahan permission Pengaturan Core

## Perubahan

- `access_settings` tidak lagi menjadi bypass untuk halaman pengguna dan role.
- Ditambahkan permission role: `view_role`, `view_any_role`, `create_role`, `update_role`, dan `delete_role`.
- Ditambahkan `view_audit_trail` untuk halaman audit trail global.
- Policy pengguna dan role sekarang memakai permission CRUD masing-masing; `super_admin` tetap bypass.
- Audit trail global memakai `view_audit_trail`, dengan permission tersebut hanya diprovision ke `super_admin` pada baseline.
- Navigation hanya menampilkan menu yang memiliki permission terkait.

## Verifikasi

- Migration incremental `2026_10_23_000004_provision_core_authorization_permissions`.
- Test memastikan `access_settings` saja tidak memberi akses ke pengguna, role, atau audit trail.
