# 2026-10-28 — Perbaikan lebar badge status detail HRIS

## Perubahan

- Memperbaiki CSS ringkasan detail pengajuan cuti/sakit agar badge status memakai `display: inline-block` dan hanya selebar labelnya.
- Memperbarui requirement serta checklist UI modul HRIS.

## Verifikasi

- Menelusuri selector `.workspace-leave-detail-stat` dan menemukan aturan umum `span { display: block; }` sebagai penyebab badge memenuhi kolom.
- Build asset frontend dijalankan setelah perubahan.

## Risiko tersisa

- Tidak ada perubahan pada data, workflow, authorization, atau penyimpanan dokumen.
