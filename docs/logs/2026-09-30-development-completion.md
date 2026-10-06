# Development Completion — 2026-09-30

## Scope

Development aplikasi file-organizer dan integrasi project-sso diselesaikan. Docker, Docker Compose, CI/CD, deployment, OCR, dan backup/restore production tetap di luar scope sesuai keputusan terakhir.

## Implemented

- Catalog Filament untuk company, project, vendor, dan document type.
- Company-aware document query dan policy untuk akses data.
- Upload private MinIO dengan checksum, duplicate detection, versioning, dan audit event.
- Metadata tags, retention date, legal hold field.
- Relasi dokumen generic yang mendukung PO/invoice/receipt melalui relation_type.
- Audit event untuk upload, relasi, verifikasi, dan bundling.
- Bundling berdasarkan selected documents maupun filter query, ZIP manifest, expiry, dan authorized download.
- SSO PKCE/state callback, external identity mapping, group-to-local-role mapping whitelist, coordinated logout.
- File Organizer enforced SSO-only login: /admin/login redirects to project-sso and does not expose a local password form.
- project-sso discovery, userinfo, JWKS, Passport OAuth client, dan logout endpoint.

## Validation

- file-organizer: php artisan test — 26 tests, 52 assertions passed.
- file-organizer infrastructure smoke — Redis dan MinIO passed.
- project-sso: php artisan test — 15 tests, 31 assertions passed.
- project-sso infrastructure smoke — Redis dan MinIO passed.
- Filament route discovery dan Laravel optimize clear passed.

## Remaining outside this completion

- Docker/CI/CD/deployment.
- Full production OIDC ID-token/consent UX, MFA, admin client management, dan centralized SSO audit.
- OCR/full-text extraction.
- Saved-search Filament CRUD (storage model and filter service foundation sudah tersedia).
- Queue retry/idempotency, monitoring, backup/restore drill, dan release runbook production.
- Quarantine malware scanning, preview/thumbnail, serta legal retention executor.
