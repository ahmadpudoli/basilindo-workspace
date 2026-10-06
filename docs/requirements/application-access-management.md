# Requirement — Pengelolaan Application Access

## Tujuan

Administrator SSO dapat mengelola akses user ke aplikasi internal tanpa harus
memanipulasi database langsung.

## Acceptance criteria

- Daftar `/admin/application-accesses` menampilkan aksi edit, hapus, dan ubah
  status untuk setiap akses.
- Edit dapat mengubah user, aplikasi, role, dan status dengan validasi boundary.
- Tombol status memakai konfirmasi dan hanya menghasilkan status `active` atau
  `suspended`.
- Hapus memakai konfirmasi, hanya dapat dilakukan oleh administrator yang
  memiliki permission, dan tidak menghapus aplikasi/user master.
- Kombinasi aplikasi, user, dan role tetap unik setelah create maupun edit.
- Semua create, update, perubahan status, dan delete menulis audit metadata
  berupa actor ID, access ID, action, dan status; tidak mencatat credential atau
  isi data sensitif.
- Pengujian mencakup model key, authorization, validasi status, dan aksi CRUD.
