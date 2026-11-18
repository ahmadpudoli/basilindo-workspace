# Otorisasi dan visibilitas cetak reimbursement

Tanggal: 2026-11-18

## Perubahan

- Menyelaraskan aksi Cetak pada ringkasan dengan policy endpoint cetak: hanya ditampilkan kepada pemilik/pemohon yang dapat melihat klaim.
- Endpoint tetap membatasi cetak pada reimbursement dengan persetujuan Finance dan status akhir yang diizinkan.
- Memperjelas requirement dan checklist.

## Verifikasi

- `git diff --check`.

## Risiko tersisa

- Test otomatis tidak dijalankan untuk perubahan ini.
