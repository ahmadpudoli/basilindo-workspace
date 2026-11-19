# Pemisahan filter status ringkasan klaim

## Perubahan

- Opsi status pada ringkasan Reimbursement tidak lagi menampilkan `awaiting_accountability`.
- Opsi status pada ringkasan Cash Advance tidak lagi menampilkan `pending_payment` dan `paid`.
- Status yang dikirim manual melalui query string divalidasi terhadap opsi untuk jenis aktif, sehingga pilihan lintas workflow tidak diterapkan.
- Halaman Semua dan filter dengan lebih dari satu jenis tetap menggunakan gabungan status.

## Verifikasi

- Meninjau daftar status pada model Claim dan transisi workflow; tahap pembayaran khusus Reimbursement, sedangkan pertanggungjawaban khusus Cash Advance.
- Tidak menjalankan test suite.

## Risiko tersisa

- Kedua workflow tetap berbagi status approval umum seperti `submitted`, `pending_manager`, `pending_finance`, `pending_final`, `approved`, `needs_revision`, `rejected`, dan `completed`.
