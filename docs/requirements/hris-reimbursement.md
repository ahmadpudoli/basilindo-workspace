# Requirement Reimbursement HRIS

Status: implementasi fase awal

## Tujuan

Menyediakan pengajuan reimbursement berbasis rincian biaya dengan approval
berjenjang sesuai scope perusahaan dan proses pembayaran terpisah oleh Finance.

## Aturan bisnis

- Satu pengajuan memiliki satu perusahaan, satu karyawan, dan satu nomor pengajuan.
- Pada form self-service, karyawan selalu diambil dari employee yang terhubung ke
  akun login; user tidak memilih karyawan dari dropdown.
- Form menampilkan atasan langsung dan approver Finance perusahaan sebagai informasi
  jalur approval.
- Pengajuan reimbursement dapat memuat beberapa rincian biaya.
- Total pengajuan dihitung dari rincian biaya di server, bukan dari input total manual.
- Status awal setelah validasi adalah `pending_manager`.
- Alur dasar adalah Atasan -> Finance -> Disetujui -> Menunggu pembayaran -> Dibayar -> Selesai.
- Approval tambahan dapat diarahkan ke approver final berdasarkan threshold nominal yang aktif pada perusahaan pengajuan.
- Jika threshold tambahan tidak dikonfigurasi, pengajuan tidak memiliki tahap approval tambahan.
- Jika threshold terpenuhi tetapi approver final tidak dikonfigurasi, pengajuan ditolak pada boundary dan tidak boleh melewati approval.
- Metode pembayaran hanya dapat dicatat oleh Finance setelah seluruh approval selesai.
- Semua query, approval, dan pembayaran wajib menggunakan company scope pengajuan.
- Pengajuan cash advance tetap dapat masuk tahap pertanggungjawaban setelah pencairan.

## Field fase pertama

Header: nomor pengajuan, karyawan, perusahaan, tipe transaksi, department, project,
client, PO number, tujuan/keterangan.

Rincian: tanggal nota, tanggal kegiatan, kategori, deskripsi, mata uang, nominal,
dan reference lampiran.

Pembayaran: metode pembayaran, penerima, bank, nomor rekening, tanggal pembayaran,
nominal dibayar, referensi pembayaran, dan bukti pembayaran.

## Acceptance criteria

- User dapat membuat reimbursement dengan minimal satu rincian biaya.
- User tidak perlu memilih karyawan dan sistem menggunakan employee dari sesi login.
- Form menampilkan nama atasan dan Finance approver yang aktif dalam scope perusahaan.
- Server menolak nominal rincian nol/negatif dan menghitung total secara konsisten.
- User tidak dapat memilih karyawan atau project di luar company scope.
- Pengajuan baru masuk `pending_manager`, bukan `submitted`.
- Approval atasan mengubah status menjadi `pending_finance`.
- Approval Finance mengubah status menjadi `approved` jika tidak ada threshold final.
- Approval Finance mengubah status menjadi `pending_final` jika threshold aktif terpenuhi.
- Pengajuan dengan threshold terpenuhi tanpa approver final aktif tidak dapat disetujui.
- Approver final yang bukan konfigurasi perusahaan tidak dapat memproses pengajuan.
- Finance dapat mencatat pembayaran hanya pada pengajuan berstatus `approved` atau `pending_payment`.
- Setelah pembayaran valid dicatat, status menjadi `paid`, lalu dapat ditandai `completed`.
- Aksi approval dan pembayaran mencatat approval/audit event tanpa menyimpan isi dokumen sensitif di log.
- Form memiliki validasi, empty state, dan pesan error yang dapat dibaca.

