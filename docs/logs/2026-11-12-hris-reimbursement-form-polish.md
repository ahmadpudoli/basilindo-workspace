# 2026-11-12 — Penyederhanaan form reimbursement

## Perubahan

- Mengkhususkan judul dan identitas halaman untuk pengajuan reimbursement.
- Memindahkan informasi tetap jenis pengajuan ke panel informasi di kolom kiri dan menghapus pilihan reimbursement/cash advance dari UI.
- Memperbarui pilihan Keperluan menjadi kartu visual dengan ikon, deskripsi, indikator pilihan aktif, dan tata letak responsif.
- Memperbarui requirement dan checklist HRIS reimbursement.

## Verifikasi

- Meninjau perubahan Blade, CSS, requirement, dan checklist melalui diff.
- Test suite dan build asset tidak dijalankan karena permintaan hanya mencakup perubahan tampilan dan dokumentasi.

## Risiko tersisa

- Cash advance masih belum tersedia sebagai alur pengajuan; akan membutuhkan form dan proses pertanggungjawaban tersendiri.
