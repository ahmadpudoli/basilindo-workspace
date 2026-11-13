# 2026-11-12 — Label status persetujuan klaim

## Perubahan

- Mengubah label `pending_manager` menjadi “Menunggu persetujuan atasan”.
- Mengubah label `pending_finance` menjadi “Menunggu persetujuan Finance”.
- Menyamakan label pada atribut status model dan filter daftar klaim tanpa mengubah nilai status internal.
- Mengarahkan kolom Status tabel daftar klaim ke atribut label model; sebelumnya tabel masih mengubah kode internal menjadi teks mentah.
- Memperbarui requirement dan checklist reimbursement.

## Verifikasi

- Kompilasi view Blade, pemeriksaan sintaks PHP, dan `git diff --check`.

## Risiko tersisa

- Tidak ada perubahan pada transisi workflow atau data status.
