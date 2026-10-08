# Requirement: Basilindo Workspace single application

## Keputusan bisnis

Untuk fase internal saat ini, seluruh modul berjalan sebagai satu aplikasi
Laravel: Core/Identity, Company, CRM, Project Management, dan Document
Management. Login utama memakai session authentication Laravel/Filament pada
database Core yang sama.

`project-sso` tidak lagi menjadi dependency runtime Workspace. Kode OIDC boleh
dipertahankan sebagai adapter integrasi masa depan, tetapi default deployment
single-app harus dapat berjalan tanpa SSO, Passport, redirect antar aplikasi,
atau application-access projection.

## Acceptance criteria

- `/login` menampilkan login lokal dan tidak redirect ke SSO.
- User dapat dibuat oleh admin Core/Users dan langsung diberi role aplikasi.
- Semua modul memakai session, user, company membership, role, dan permission
  yang sama.
- CRM, Project, dan Document tetap menjaga policy/company scope.
- SSO legacy dapat dimatikan melalui `SSO_ENABLED=false` tanpa mengganggu modul.
- Migration existing install tidak menghapus user, role, company, atau data bisnis.

## Dampak migrasi

User lama dari SSO dipertahankan berdasarkan email/record Core yang sudah ada.
Password lokal harus diatur melalui reset password atau provisioning admin.
Entitlement aplikasi lama tidak lagi menjadi filter daftar user; company
membership dan role lokal menjadi sumber authorization.
