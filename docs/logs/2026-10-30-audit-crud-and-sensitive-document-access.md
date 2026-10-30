# Audit CRUD dan Akses Dokumen Sensitif

## Perubahan

- Menambahkan observer audit terpusat untuk Create, Update, dan Delete pada resource Company, CRM, Project Management, HRIS, dan resource pendukung Document Management.
- Snapshot perubahan hanya menyimpan metadata/atribut aman, membatasi string panjang, dan meredaksi password, token, secret, binary, serta field sejenis.
- Menambahkan audit akses detail/preview/download dokumen Workspace, bundle, dan dokumen karyawan.
- Audit preview dokumen yang sudah tersedia tetap digunakan untuk mencatat akses view tanpa menyimpan isi file.

## Verifikasi

- Registrasi observer menggunakan daftar model bisnis eksplisit agar model audit dan event teknis tidak ikut diaudit.
- Setiap download dicatat setelah authorization dan stream storage berhasil dibuka.
- `git diff --check` dan lint PHP pada file perubahan dijalankan.

## Risiko tersisa

- Event dari data lama sebelum perubahan tidak dapat dibuat ulang; hanya aktivitas setelah deployment yang tercatat.
- Audit event untuk operasi pivot yang tidak menghasilkan model event perlu ditambahkan secara khusus bila aktivitas tersebut diperlakukan sebagai perubahan bisnis mandiri.
