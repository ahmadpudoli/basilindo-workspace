# Requirement: Modul Pengaturan Workspace

## Tujuan

Menyediakan satu modul administratif untuk mengatur modul aktif, akun pengguna,
role/permission, serta scope perusahaan yang berlaku lintas modul Workspace.

## Aturan akses

- Modul hanya muncul pada dashboard, launcher, dan sidebar untuk `super_admin` atau user yang memiliki permission `access_settings`.
- Pengaturan modul aktif berlaku global untuk Workspace; minimal satu modul bisnis harus aktif.
- Modul bisnis yang dinonaktifkan tidak muncul pada dashboard/launcher dan shortcut `/workspace/{module}` ditolak.

## User dan data scope

- User adalah identitas shared Core yang dipakai semua modul.
- Admin dapat menetapkan role, permission tambahan langsung pada user, dan satu atau beberapa perusahaan yang boleh diakses user.
- Relasi user–company menjadi scope lintas modul; policy domain tetap menjadi enforcement terakhir.
- Relasi employee dengan tepat satu perusahaan akan dihubungkan ketika modul HRIS dan entitas employee tersedia. Itu berbeda dari company access scope yang boleh multi-company.

## Acceptance criteria

- Super admin dapat membuka `/workspace/settings` dan mengatur modul aktif.
- User dengan `access_settings` dapat membuka pengaturan tanpa menjadi super admin.
- User tanpa akses menerima 403 dan tidak melihat modul Pengaturan.
- User dapat memiliki role, direct permission tambahan, dan scope LTN serta Legospay.
- Perubahan scope tersimpan pada shared `company_user` dan dipakai policy modul.
