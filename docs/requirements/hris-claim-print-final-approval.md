# Approval final pada cetakan reimbursement

## Acceptance criteria

- Jika claim memiliki `final_approved_at`, cetakan menampilkan blok Persetujuan Final pada bagian penandatangan setelah persetujuan Atasan dan Finance.
- Blok menampilkan status disetujui, nama approver final, dan waktu persetujuan menurut zona waktu aplikasi.
- Blok final tidak ditampilkan untuk pengajuan yang tidak memiliki approval final.
- Informasi final tidak diulang pada ringkasan; nama dan waktu approver berada pada area persetujuan dokumen.
- Empat blok penandatangan memakai susunan dua kolom agar tetap terbaca dalam pratinjau A4; pada layar kecil susunannya menjadi satu kolom.
- Area tanda tangan dijaga tetap utuh saat cetak.
- Informasi pengajuan menampilkan status pembayaran. Jika statusnya `paid`, rincian metode, penerima, nominal, tanggal, referensi, dan tujuan transfer/sumber kas tampil hanya bagi pemegang `process_hris_payment` atau `super_admin`.
- Jika pembayaran berstatus `paid` dan pengguna berhak melihat detail pembayaran, bukti pembayaran ikut dilampirkan setelah seluruh dokumen pendukung rincian biaya; gambar dan PDF dirender mengikuti mekanisme lampiran cetak yang tersedia.
- Rincian pembayaran dikelompokkan sebagai subbagian di dalam panel informasi pengajuan yang sama; status pembayaran tampil sebagai badge dengan warna sesuai status.
- Rincian biaya dan panel tanda tangan/persetujuan tampil sebagai panel terpisah pada lembar rincian yang sama.
- Dokumen menampilkan nama pengguna pencetak serta tanggal dan waktu cetak dalam zona waktu aplikasi.
