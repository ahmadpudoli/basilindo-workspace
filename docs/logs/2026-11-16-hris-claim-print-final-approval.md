# Penandatangan approval final pada cetakan klaim — 2026-11-16

## Perubahan

- Menambahkan blok Persetujuan Final pada kelompok tanda tangan reimbursement bila `final_approved_at` tersedia.
- Blok menampilkan nama approver final dan waktu persetujuan; ringkasan tidak menduplikasi informasi tersebut.
- Susunan menjadi dua kolom ketika empat penandatangan tampil sehingga lebih proporsional dalam pratinjau A4.
- Menjaga blok persetujuan tetap utuh saat pagination cetak.

## Verifikasi

- Pemeriksaan kode menunjukkan controller cetak sudah eager-load relasi `finalApprover`.
- Belum menjalankan test atau render cetak browser.
