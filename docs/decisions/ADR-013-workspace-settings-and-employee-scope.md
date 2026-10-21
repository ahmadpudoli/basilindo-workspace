# ADR-013 — Workspace settings dan employee scope

Status: accepted.

Modul Pengaturan menjadi boundary administratif di Core/Identity. Akses modul
diberikan kepada `super_admin` atau permission granular `access_settings`. Daftar
modul aktif disimpan sebagai global setting sehingga dashboard, launcher, dan
shortcut modul memiliki sumber konfigurasi yang sama.

Company access user tetap memakai relasi shared `company_user` dan boleh multi-company
untuk kebutuhan scope data. Relasi employee yang wajib tepat satu perusahaan tidak
dipaksakan ke tabel user sebelum modul HRIS memiliki entitas employee dan lifecycle
sendiri.
