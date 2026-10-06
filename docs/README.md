# Dokumentasi File Organizer

Dokumentasi ini menjadi sumber kebenaran proyek selain kode. Agen wajib membaca dokumen yang relevan sebelum bekerja dan memperbarui checklist serta log setelah bekerja.

## Peta dokumen

- [Arsitektur](architecture.md)
- [Domain model](domain-model.md)
- [Keamanan](security.md)
- [SSO/OIDC](sso.md)
- [Roadmap](roadmap.md)
- [Checklist fitur](feature-checklist.md)
- [Keputusan arsitektur](decisions.md)
- [Requirement shared core](requirements/shared-core-architecture.md)
- [Log pekerjaan](logs/)

## Lingkup MVP

MVP berfokus pada penyimpanan dokumen privat, metadata terstruktur, pencarian multi-kriteria, relasi antar-dokumen, review manual, dan download bundel. OCR, automated extraction, integrasi akuntansi, dan machine learning diperlakukan sebagai fase lanjutan sampai alur bisnis dan kualitas data stabil.

## Cara memakai dokumentasi

1. Mulai dari `agents.md` di root untuk urutan kerja dan aturan implementasi.
2. Gunakan `architecture.md` dan `domain-model.md` saat membuat migration/service.
3. Gunakan `security.md` dan `sso.md` sebelum menyentuh authentication, authorization, upload, atau download.
4. Ambil pekerjaan dari `feature-checklist.md` sesuai urutan roadmap.
5. Catat pekerjaan pada file log dengan format tanggal `YYYY-MM-DD-agent-name.md`.
