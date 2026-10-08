# ADR-012 — Single application identity untuk Basilindo Workspace

- **Status:** Accepted
- **Tanggal:** 2026-10-08
- **Menggantikan:** ADR-005 sebagai keputusan runtime Workspace

## Keputusan

`app/file-organizer` menjadi satu-satunya aplikasi aktif untuk lima modul dan
memakai login lokal Laravel/Filament. `project-sso` tidak diperlukan untuk
operasi internal single-app. OIDC tetap dapat dipertahankan sebagai adapter
opsional bila kelak ada aplikasi eksternal atau federation perusahaan.

## Alasan

CRM, project, dokumen, user, company, dan transaksi lintas modul sangat rapat.
Satu session dan satu source of truth menghilangkan redirect, cookie lintas
port, projection access, dan sinkronisasi identity yang tidak diperlukan.

## Konsekuensi

- Deployment development utama cukup satu app, database, Redis, dan MinIO.
- User dibuat dan dikelola pada Core/Users.
- Role aplikasi tetap dibedakan dari company scope.
- SSO legacy tidak boleh menjadi syarat boot atau login.
- Jika aplikasi kedua muncul di masa depan, adapter OIDC dapat diaktifkan
  kembali tanpa mengubah domain module.
