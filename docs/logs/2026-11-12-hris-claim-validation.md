# 2026-11-12 — Validasi form reimbursement

## Perubahan

- Menambahkan validasi client-side pada form reimbursement sebelum POST untuk kedua aksi: Simpan draft dan Kirim pengajuan.
- Validasi client-side mencakup konteks pengeluaran, kategori master, nominal positif maksimal dua desimal, deskripsi, konsistensi mata uang, dan referensi project/opportunity kondisional.
- Memperkuat validasi Form Request server untuk tipe referensi yang sah dan referensi project/opportunity wajib saat dipilih.
- Memperbarui requirement reimbursement dan checklist fitur.

## Verifikasi

- Meninjau handler submit global: validasi client-side berjalan sebelum dialog konfirmasi kirim, dan Form Request tetap dipakai pada endpoint simpan/update.
- Pemeriksaan otomatis test/build tidak dijalankan.

## Risiko tersisa

- Validasi akses, status aktif kategori, dan scope project/opportunity tetap menjadi tanggung jawab service/policy server; validasi browser hanya membantu pengguna memperbaiki input lebih awal.
