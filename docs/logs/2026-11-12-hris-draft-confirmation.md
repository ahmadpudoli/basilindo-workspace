# 2026-11-12 — Perapian draft reimbursement

## Perubahan

- Menghapus panel ringkasan draft di atas daftar klaim karena aksi draft sudah tersedia di setiap baris tabel.
- Memastikan aksi Kirim pengajuan meminta konfirmasi setelah validasi client-side. Menambahkan fallback konfirmasi browser jika shared modal tidak tersedia pada layout.
- Memperbarui requirement dan checklist reimbursement.

## Verifikasi

- Meninjau urutan event submit: validasi client-side berjalan pada capture listener sebelum konfirmasi; submit baru dilanjutkan setelah persetujuan.
- `git diff --check`.

## Risiko tersisa

- Belum menjalankan pemeriksaan browser langsung pada halaman edit.
