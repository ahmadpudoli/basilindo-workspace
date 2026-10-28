# 2026-10-28 — Penyamaan layout form revisi cuti

## Perubahan

- Menambahkan view form revisi dengan struktur dua kolom yang sama seperti form create.
- Menyamakan panel kuota, tanggal dinamis, alasan, dokumen pendukung, informasi karyawan, atasan, HR, dan area aksi.
- Jenis pengajuan tetap read-only saat revisi dan nilai `type` dikirim melalui hidden input.
- Mempertahankan validasi client-side, dukungan dokumen lama, dan endpoint resubmit.

## Verifikasi

- Controller revisi diarahkan ke view layout baru tanpa mengubah service atau Form Request.
- Build asset frontend dan pemeriksaan Blade akan dijalankan setelah perubahan.

## Risiko tersisa

- View lama tetap dipertahankan sebagai fallback file, tetapi tidak lagi dipakai oleh controller revisi.
