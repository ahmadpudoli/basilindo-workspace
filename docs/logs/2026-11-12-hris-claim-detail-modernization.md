# 2026-11-12 — Modernisasi detail reimbursement

## Perubahan

- Memperbarui hierarki visual detail pengajuan dengan header ringkas, ringkasan status/pemohon/total, dan panel konteks yang compact.
- Menambahkan kolom tanggal kegiatan pada rincian biaya.
- Mengganti aksi unduh langsung menjadi Preview dan menampilkan dialog **Preview Bukti Dokumen** dengan data biaya di kiri serta dokumen di kanan.
- Memakai endpoint dokumen privat yang sama dengan mode inline terotorisasi dan menerapkan dialog responsif.
- Header dialog hanya menggunakan judul **Preview Bukti Nota**, tanpa kicker tambahan.
- Memperbarui requirement serta checklist fitur.

## Verifikasi

- Meninjau template dan endpoint attachment untuk memastikan preview memakai parameter inline serta tidak mengubah authorization.
- `git diff --check` dan `npm.cmd run build`.

## Risiko tersisa

- Belum memeriksa tampilan halaman langsung di browser.
