# Pemisahan status approval dan pembayaran reimbursement

## Perubahan

- Menambahkan kolom nullable `claims.payment_status` melalui migration incremental. Data lama `pending_payment` dimigrasikan menjadi `status=approved` dan `payment_status=pending_payment`; data lama `paid`/`completed` menjadi `status=approved` dan `payment_status=paid`; reimbursement approved lama mendapat status pembayaran pending.
- Workflow menyetel pembayaran ke `pending_payment` saat reimbursement mencapai status pengajuan `approved`, setelah approval final yang diperlukan selesai. Sebelum itu nilainya `null`.
- Pencatatan pembayaran hanya mengubah `payment_status` menjadi `paid`; status pengajuan tetap `approved`. Tahap dan aksi Tandai selesai untuk reimbursement dihapus.
- Filter dan tabel daftar/ringkasan memisahkan Status pengajuan dan Status pembayaran. Status pembayaran tidak ditampilkan untuk Cash Advance; detail klaim juga menunjukkan keduanya.
- KPI serta pemeriksaan cetak dan dashboard menghitung status pengajuan berdasarkan approval, tanpa mencampur status pembayaran.

## Verifikasi

- Menelusuri workflow approval, pembayaran Finance, query filter, tabel daftar/ringkasan, dan detail.
- Migration incremental `2026_11_19_000001_separate_claim_payment_status` berhasil diterapkan pada database development aktif.
- Tidak menjalankan test suite.

## Risiko tersisa

- Migration yang sama perlu dijalankan pada setiap environment selain database development aktif. Rollback mempertahankan kolom dan data pembayaran agar tidak menghapus riwayat.
- Status `completed` tetap digunakan sebagai status penyelesaian Cash Advance.
