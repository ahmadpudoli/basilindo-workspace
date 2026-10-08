# Panduan Pengguna: Pembuatan Akun di Basilindo Workspace

## Tujuan

Panduan ini menjelaskan pembuatan akun pada satu aplikasi modular monolith. CRM, Project Management, dan Document Management menggunakan identitas Core yang sama.

## Konsep penting

Workspace adalah pusat identitas Basilindo. Satu user dapat memakai beberapa modul dengan role dan company scope yang sesuai.

## Membuat user baru

1. Buka Workspace, misalnya `http://localhost:8001/admin`.
2. Masuk sebagai administrator yang memiliki permission user management.
3. Buka menu Users, lalu pilih User baru.
4. Isi nama, email, dan password awal.
5. Simpan user.

User baru tersimpan di Core dan dapat digunakan oleh semua modul setelah diberi role.

## Menetapkan role dan scope

1. Buka halaman edit user.
2. Pilih role sesuai tugas, misalnya `crm_member`, `project_member`, atau role admin.
3. Tambahkan company membership bila user perlu melihat data perusahaan tertentu.
4. Simpan perubahan.

Role menentukan kemampuan modul, sedangkan company membership menentukan batas data.

## Contoh alur

User A dibuat di Workspace. Admin cukup memberi role Project Management dan company membership yang diperlukan. Jika User A juga perlu CRM atau Document Management, tambahkan role modul tersebut pada user yang sama.

Hasilnya:

- satu identitas Workspace;
- satu user Core;
- beberapa role modul sesuai kebutuhan;
- tidak ada duplikasi akun.

## Login ke aplikasi

1. Buka Workspace, misalnya `http://localhost:8001/admin`.
2. Masukkan email dan password pada `/login`.
3. Buka modul yang tampil sesuai role user.

Workspace tidak bergantung pada `project-sso` untuk operasi normal. Jika adapter OIDC/SSO
diaktifkan, adapter tersebut hanya metode login tambahan; authorization tetap dikelola Workspace.

## Perubahan data user

Nama, email, password, role, verifikasi, dan company scope diubah dari menu Users Workspace.
Tidak perlu membuat user terpisah untuk modul lain.

## Aturan keamanan

- Jangan membuat akun kedua dengan email yang sama untuk aplikasi berbeda.
- Cabut role atau company membership saat user tidak lagi memerlukannya.
- Gunakan role dengan prinsip least privilege.
- Gunakan prinsip least privilege dan audit perubahan role.

## Ringkasan lokasi

| Kebutuhan | Lokasi |
|---|---|
| Membuat user | Workspace → Users → User baru |
| Mengelola role | Workspace → Users → Edit |
| Mengelola company scope | Workspace → Users → Companies |
| Login utama | Workspace `/login` |
| Audit akses | Workspace dan audit event modul |

## Catatan migrasi

Cookie sesi lama dari aplikasi SSO terpisah dapat menyebabkan redirect lama pada browser.
Setelah deployment single-app, lakukan logout atau hapus cookie lama sekali, lalu buka
kembali `/admin/login`. Pengguna biasa tidak perlu melakukan konfigurasi teknis tersebut.
