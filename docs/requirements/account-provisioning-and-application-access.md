# Requirement: Provisioning akun dan akses aplikasi

## Prinsip bisnis

- Basilindo Workspace adalah satu aplikasi modular monolith dan sumber kebenaran identitas user.
- User dibuat dan dikelola dari modul Core/Users pada Workspace.
- Role dan membership perusahaan dikelola di Workspace sesuai kewenangan admin.
- SSO/OIDC hanya adapter opsional untuk integrasi eksternal, bukan dependency runtime.

## Alur yang disetujui

### User baru

1. Admin Workspace membuat user dari menu Users.
2. Admin menetapkan role dan membership perusahaan bila diperlukan.
3. Modul CRM, Project, dan Document memakai user Core yang sama tanpa membuat identitas baru.

### User yang sudah ada

1. Admin Workspace mencari user yang sudah ada dari menu Users.
2. Admin menambahkan role atau membership modul yang diperlukan.
3. Operasi bersifat idempotent; role atau membership yang sudah ada tidak digandakan.

### Login

Login lokal membuat session Workspace. Jika adapter SSO diaktifkan, SSO hanya menjadi metode autentikasi tambahan; authorization tetap diputuskan oleh policy dan role Workspace.

## Batasan aplikasi

- Halaman Users Workspace menyediakan pembuatan dan pengelolaan user Core.
- Perubahan nama, email, password, verifikasi, role, dan company scope dikelola di Workspace.
- Tidak ada menu User/Company terpisah untuk aplikasi client karena semua modul berada di satu aplikasi.
- Akses root Workspace ketika sesi user masih aktif mengarahkan user ke halaman utama admin.

## Acceptance criteria

- Admin dapat membuat user dari `/admin/users/create` dan user tersimpan di Core.
- User yang sama dapat digunakan CRM, Project Management, dan Document Management tanpa duplikasi identitas.
- Role dan company scope menentukan data serta menu yang dapat diakses.
- Workspace dapat berjalan tanpa `project-sso` dan tanpa redirect login lintas aplikasi.
