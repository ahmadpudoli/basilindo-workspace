# 2026-10-28 — Tipe field nilai parameter HRIS

## Perubahan

- Field nilai Pengaturan HRIS kini berubah sesuai parameter yang dipilih.
- Cut-off pengajuan menggunakan input tanggal.
- Kuota cuti, maksimum lembur, dan ambang approval final menggunakan input angka.
- Petunjuk pengisian ditampilkan di bawah field nilai.

## Verifikasi

- Validasi server tetap menjadi sumber kebenaran untuk format dan rentang nilai.
- Parameter yang belum memiliki tipe khusus tetap memakai input teks.

## Risiko tersisa

- Penambahan parameter baru perlu menambahkan mapping tipe field pada view Pengaturan HRIS.
