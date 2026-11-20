# Filter keputusan pribadi ringkasan klaim — 2026-11-20

## Perubahan

- Menambahkan filter Riwayat approval “Disetujui oleh saya” dan “Ditolak oleh saya” pada ringkasan Reimbursement dan Cash Advance.
- Filter memakai approval event `approve`/`reject` oleh pengguna login dan diterapkan sebelum agregasi KPI serta pagination.
- Tetap menerapkan scope perusahaan, jenis klaim, dan filter ringkasan lain yang sudah tersedia.
- Memperbarui requirement dan checklist.

## Verifikasi

- `git diff --check`.
- Belum menjalankan test atau build.

## Risiko tersisa

- Hasil filter belum diperiksa langsung di browser atau database aplikasi.
