# Dokumentasi Basilindo Workspace

Dokumentasi ini menjadi sumber kebenaran proyek selain kode. Agen wajib membaca dokumen yang relevan sebelum bekerja dan memperbarui checklist serta log setelah bekerja.

## Peta dokumen

- [Arsitektur](architecture.md)
- [Domain model](domain-model.md)
- [Keamanan](security.md)
- [Identitas dan login lokal](identity.md)
- [Roadmap](roadmap.md)
- [Checklist fitur](feature-checklist.md)
- [Keputusan arsitektur](decisions.md)
- [Requirement provisioning akun dan akses aplikasi](requirements/account-provisioning-and-application-access.md)
- [Requirement Basilindo Workspace modular monolith](requirements/workspace-modular-monolith.md)
- [Requirement projection application access](requirements/application-access-projection.md)
- [Panduan provisioning akun (Markdown)](panduan-provisioning-akun.md)
- [Panduan provisioning akun (Markdown canonical)](panduan-provisioning-akun.md)
- `panduan-provisioning-akun.docx` adalah artefak versi sebelumnya dan perlu diregenerasi sebelum dibagikan.
- [Requirement shared core](requirements/shared-core-architecture.md)
- [Log pekerjaan](logs/)
- [Runbook backup dan restore](operations/backup-restore.md)
- [Monitoring](operations/monitoring.md)
- [Requirement health dan monitoring](requirements/monitoring-and-health.md)
- [Requirement single-app identity](requirements/single-app-identity.md)
- [Requirement Workspace runtime tunggal](requirements/workspace-runtime.md)
- [Release checklist dan rollback](operations/release-checklist.md)
- CI baseline: `.github/workflows/workspace-ci.yml`

## Lingkup MVP

MVP berfokus pada penyimpanan dokumen privat, metadata terstruktur, pencarian multi-kriteria, relasi antar-dokumen, review manual, dan download bundel. OCR, automated extraction, integrasi akuntansi, dan machine learning diperlakukan sebagai fase lanjutan sampai alur bisnis dan kualitas data stabil.

## Cara memakai dokumentasi

1. Mulai dari `agents.md` di root untuk urutan kerja dan aturan implementasi.
2. Gunakan `architecture.md` dan `domain-model.md` saat membuat migration/service.
3. Gunakan `security.md` dan `sso.md` sebelum menyentuh authentication, authorization, upload, atau download.
4. Ambil pekerjaan dari `feature-checklist.md` sesuai urutan roadmap.
5. Catat pekerjaan pada file log dengan format tanggal `YYYY-MM-DD-agent-name.md`.
