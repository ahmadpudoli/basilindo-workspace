# ADR-013 — Workspace sebagai runtime tunggal

- **Status:** Accepted
- **Tanggal:** 2026-10-08
- **Menggantikan:** ADR-012 sebagai keputusan implementasi runtime

## Keputusan

Runtime dipindahkan dari `app/file-organizer` ke aplikasi baru `app/workspace`.
Workspace adalah satu Laravel Filament modular monolith yang memuat Core,
Company, CRM, Project Management, dan Document Management. `project-sso`
dihapus dan login eksternal tidak menjadi bagian dari runtime.

## Alasan

Modul-modul bertransaksi lintas domain secara sering. Satu application boundary,
session, database, queue, dan deployment menghindari redirect, sinkronisasi
identity, cookie lintas port, dan duplikasi authorization.

## Konsekuensi

- URL development tunggal adalah `http://localhost:8080`.
- User, role, permission, dan company scope dimiliki Core Workspace.
- Modul tetap dipisahkan melalui namespace, service, policy, event, job, dan
  permission; modular bukan berarti aplikasi terpisah.
- Folder legacy dipertahankan hanya sampai migrasi tervalidasi, lalu tidak boleh
  dijalankan.
