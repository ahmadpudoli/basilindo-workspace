# 2026-11-12 — Informasi client dan kontak pada detail reimbursement

## Perubahan

- Menambahkan eager loading relasi client Project dan account/contact Opportunity pada detail claim.
- Panel Konteks pengajuan menampilkan client dari referensi terpilih dan nama/email kontak opportunity bila tersedia.
- Project tidak memiliki relasi kontak langsung; kontak ditampilkan sebagai kosong jika referensi yang dipilih hanya Project.
- Memperbarui requirement dan checklist.

## Verifikasi

- Meninjau relasi model Project dan Opportunity untuk memastikan sumber client/contact yang ditampilkan.
- Kompilasi view Blade dan pemeriksaan diff.

## Risiko tersisa

- Kontak untuk Project hanya dapat ditampilkan bila Project mempunyai relasi kontak pada model; relasi tersebut saat ini belum ada.
