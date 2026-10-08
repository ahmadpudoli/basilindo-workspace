# ADR-014 — Prefix tabel per modul

## Status

Disetujui dan migration fisik utama sudah dijalankan pada database Workspace.

## Keputusan

Workspace menggunakan satu database PostgreSQL, tetapi setiap bounded context
memiliki namespace tabel yang jelas:

- `core_`: identity, users, companies, roles, permissions, settings.
- `crm_`: accounts, contacts, leads, opportunities, activities.
- `pm_`: projects, tickets, dan data project management.
- `doc_`: documents, versions, verification, bundles, dan audit dokumen.
- `ws_`: tabel teknis runtime seperti cache, jobs, sessions, dan migration ledger.

`fo_`, `pm_` legacy, dan `ws_` domain lama sudah diproses. Tabel kosong dihapus;
tabel legacy yang masih berisi data dipindahkan ke schema `legacy` agar tetap
tersimpan dan tidak bercampur dengan runtime aktif.

## Alasan

Prefix `ws_` untuk seluruh domain menyamarkan boundary modular monolith dan
membuat tabel legacy `fo_`/`pm_` tampak sebagai bagian runtime yang sama. Prefix
modul memudahkan ownership, query review, backup selektif, dan pemeliharaan.

## Konsekuensi

Migration `2026_10_08_170000_finalize_module_table_prefixes` memvalidasi jumlah
baris, memindahkan data legacy berisi ke schema `legacy`, lalu rename tabel aktif.
Model runtime menggunakan koneksi/prefix modul. Ledger/cache lama yang bukan
data bisnis juga disimpan di schema `legacy`.
