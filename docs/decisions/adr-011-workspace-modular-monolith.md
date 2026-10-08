# ADR-011 — Basilindo Workspace sebagai modular monolith

- **Status:** Accepted
- **Tanggal:** 2026-10-08

## Keputusan

`app/file-organizer` dikembangkan menjadi Basilindo Workspace modular monolith yang memuat Core/Identity, Company, CRM, Project Management, dan Document Management. Project SSO tetap menjadi aplikasi OIDC terpisah.

## Alasan

CRM, project, dan dokumen akan sering bertransaksi bersama. Satu aplikasi client mengurangi sinkronisasi, kompleksitas deployment, dan duplikasi user/company. Boundary domain tetap dipertahankan agar modul dapat dipisahkan di masa depan bila diperlukan.

## Konsekuensi

- Satu codebase dan deployment untuk modul client.
- Database transaction lintas modul lebih mudah dilakukan.
- Setiap modul wajib menjaga service, policy, permission, dan test boundary.
- Project Management lama tetap dipakai sebagai referensi/migrasi bertahap.
- URL dan port aplikasi lama dipertahankan selama masa transisi.

## Alternatif

Membuat project baru dari nol ditolak karena meningkatkan biaya migrasi UI, storage, auth, dan data yang sudah berjalan.

