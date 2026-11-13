# Status dan aksi draft reimbursement

Tanggal: 2026-11-12

## Perubahan

- Menjaga nilai tombol submit ketika form melakukan upload bukti sebelum submit; aksi draft tidak lagi berubah menjadi submit.
- Menampilkan indikator loading pada simpan draft, unggah bukti, dan kirim pengajuan.
- Menambahkan halaman detail klaim dengan policy akses dan aksi edit/hapus untuk draft.
- Daftar draft menyediakan link detail, edit, dan hapus.
- Memulihkan satu klaim yang tanpa sengaja masuk ke `pending_manager` karena nilai aksi submit hilang, kembali ke status `draft` dan mengosongkan nomor pengajuan yang seharusnya baru dibuat saat pengiriman.

## Verifikasi

- Pemeriksaan sintaks PHP/Blade dan build frontend dilakukan setelah implementasi.
- Data aplikasi diperiksa dengan ID klaim spesifik sebelum status dipulihkan.
