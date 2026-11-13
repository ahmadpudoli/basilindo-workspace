# Requirement Master Kategori Biaya Reimbursement

Status: implementasi awal

## Tujuan

Menyediakan daftar kategori biaya terpusat yang dikelola melalui Pengaturan HRIS
dan dipakai sebagai pilihan kategori pada setiap rincian reimbursement.

## Aturan bisnis

- Kategori memiliki nama, keterangan opsional, urutan tampil, dan status aktif/arsip.
- Pengajuan baru hanya dapat memakai kategori aktif.
- Kategori yang telah dipakai tidak dihapus permanen; kategori diarsipkan agar riwayat tetap terbaca.
- Nama kategori disimpan sebagai snapshot pada rincian biaya, sedangkan ID kategori menghubungkannya ke master.
- Kategori awal mencakup Transportasi, Akomodasi, Konsumsi, Perlengkapan dan kebutuhan kantor, Komunikasi dan internet, Pengiriman dan kurir, Biaya proyek atau client, serta Lain-lain.
- Nilai kategori lama yang sudah tersimpan pada rincian biaya dipertahankan dan dipetakan saat migrasi.
- Hak akses dibagi menjadi `view_hris_expense_categories`, `create_hris_expense_categories`, `update_hris_expense_categories`, dan `delete_hris_expense_categories`; arsip/pemulihan memakai delete.
- Halaman berada di Pengaturan → Pengaturan HRIS dan hanya tampil bagi role dengan permission view atau aksi pengelolaan yang relevan.

## Acceptance criteria

- Admin dapat menambah, mengubah, mengarsipkan, dan memulihkan kategori.
- Pengguna tanpa permission view menerima 403 pada halaman; aksi tambah, ubah, arsip, dan pemulihan masing-masing memerlukan permission granular yang sesuai.
- Kategori aktif tersedia pada dropdown setiap rincian reimbursement.
- Server menolak kategori yang tidak ada atau telah diarsipkan untuk pengajuan baru.
- Form edit draft dapat menampilkan kategori arsip yang sudah terpakai; draft dapat disimpan tanpa mengganti kategori tersebut.
- Pengarsipan kategori tidak mengubah snapshot kategori pada riwayat klaim.
- Daftar master memiliki pencarian, filter status, pagination standar, dan sorting server-side.
- Role menampilkan permission kategori pada grup Core & Pengaturan.
