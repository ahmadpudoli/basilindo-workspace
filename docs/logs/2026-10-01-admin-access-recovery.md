# 2026-10-01 — Admin console dan recovery password lokal

## Requirement dan acceptance criteria

- Admin console `project-sso` dapat digunakan untuk mengelola user, role, dan akses aplikasi.
- Akun `admin@example.com` tersedia, terverifikasi, dan memiliki role `super_admin`.
- Seeder development idempoten dan dapat mengatur ulang password seluruh user SSO melalui environment tanpa menyimpan password plaintext di database.
- Password tersimpan sebagai hash dan tidak ditulis ke log.

## Perubahan

- Seeder SSO membaca `LOCAL_ADMIN_EMAIL` dan `LOCAL_ADMIN_PASSWORD` dengan default development.
- Seeder membuat/memperbarui admin, memberikan role `super_admin`, lalu melakukan reset password seluruh user SSO dengan `Hash::make`.
- Checklist role dan permission matrix diperbarui.

## Risiko tersisa

- Password default hanya aman untuk local development dan wajib diganti sebelum deployment.
- Audit login dan perubahan permission masih tercatat sebagai pekerjaan lanjutan.
