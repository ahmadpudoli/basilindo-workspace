# Requirement: Pemetaan port lokal aplikasi

## Tujuan

Satu aplikasi Workspace berjalan pada satu URL development. Aplikasi legacy tidak
menjadi bagian dari deployment aktif.

## Acceptance criteria

- Port `8000`/`8002` adalah konfigurasi legacy untuk aplikasi terpisah; runtime target kini satu Workspace app.
- Workspace melayani HTTP pada `http://localhost:8080`.
- Tidak ada redirect callback SSO atau URL aplikasi kedua.
- Root Workspace `/` mengarahkan user yang sudah login ke halaman utama admin.
- Script Composer, Octane, Docker Compose, environment example, dan test mengikuti URL Workspace tersebut.
- PostgreSQL, Redis, dan MinIO dimiliki satu Compose stack Workspace.
- Dependency app menunggu health check database, Redis, dan MinIO.
