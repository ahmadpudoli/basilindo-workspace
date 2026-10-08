# Requirement: Workspace runtime tunggal

## Keputusan

`app/workspace` adalah satu-satunya aplikasi runtime Basilindo. Lima boundary
modul berjalan dalam satu Laravel Filament application:

1. Core / Identity
2. Company
3. CRM
4. Project Management
5. Document Management

## Acceptance criteria

- Development memakai `http://localhost:8080`.
- Hanya ada satu login lokal pada `/login`.
- Tidak ada route, command, scheduler, atau dependency runtime SSO/OIDC.
- Database, Redis, queue, MinIO, session, dan deployment dimiliki Workspace.
- Kode modul berada di boundary domain yang eksplisit dan tidak membuat aplikasi
  Laravel terpisah.
- `app/file-organizer`, `app/project-management`, dan `app/project-sso` tidak
  digunakan sebagai runtime.
