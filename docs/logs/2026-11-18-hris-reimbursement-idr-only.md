# Mata uang reimbursement IDR saja

## Perubahan

- Membatasi dropdown mata uang rincian reimbursement menjadi satu pilihan: IDR.
- Membatasi validasi server untuk mata uang rincian dan fallback payload hanya menerima IDR.
- Memperbarui requirement dan checklist.

## Verifikasi

- Pemeriksaan sintaks PHP dan `git diff --check`.
- Tidak menjalankan test suite.

## Risiko tersisa

- Data historis dengan mata uang selain IDR tidak diubah.
