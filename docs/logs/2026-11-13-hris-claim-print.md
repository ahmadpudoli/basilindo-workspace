# Cetak dokumen reimbursement

## Perubahan

- Menambahkan halaman cetak reimbursement setelah persetujuan Finance dengan kop nama perusahaan, status terkini, data pemohon, referensi, rincian biaya, total, dan waktu persetujuan.
- Menambahkan aksi Cetak pada daftar klaim dan detail; tombol hanya tersedia bagi reimbursement yang telah melewati Finance.
- Endpoint cetak memakai policy `view` yang sama dengan detail klaim dan memverifikasi jenis serta status persetujuan sebelum merender dokumen.
- Melengkapi identitas dan waktu persetujuan Atasan/Finance, tanggal awal pengajuan yang dipertahankan saat revisi, serta tanda centang hijau dan tampilan kartu persetujuan.
- Menghapus rincian approver yang berulang dari ringkasan dan menambahkan area tanda tangan pemohon pada footer.
- Menghapus nama perusahaan dari ringkasan karena sudah tampil di kop.
- Pratinjau cetak memakai ukuran A4; gambar dibatasi tinggi 90 mm dan mengalir proporsional untuk menghemat kertas, sedangkan setiap halaman PDF dirender lokal sebagai lembar A4 terpisah melalui PDF.js dari endpoint privat. Tombol cetak menunggu seluruh PDF selesai diproses secara berurutan.
- PDF.js (`pdfjs-dist`) ditambahkan sebagai dependensi lokal setelah memastikan tidak ada parser PDF atau konverter server yang tersedia; keputusan dan alternatif dicatat di ADR rendering PDF.
- Menyetarakan tinggi page sheet PDF dan halaman lampiran ke area cetak A4 269 mm (A4 dikurangi margin 14 mm di setiap sisi) untuk mencegah overflow yang memicu halaman kosong.
- Memindahkan lembar PDF keluar dari wadah `.paper` lampiran. Wadah A4 sebelumnya ikut membungkus halaman PDF A4 sehingga tinggi gabungannya meluber dan memicu lembar kosong tambahan saat dicetak.
- Lampiran gambar sekarang mengikuti aliran halaman cetak dan dapat berbagi ruang di halaman yang sama; pemisah halaman baru hanya terjadi bila konten tidak muat atau untuk lembar PDF.
- Menghapus tinggi minimum satu halaman penuh dari kartu gambar pada pratinjau maupun cetak agar kartu gambar tidak menyisakan satu halaman kosong dan lampiran berikutnya dapat mengalir proporsional.
- Mengelompokkan lampiran gambar berurutan dalam satu lembar preview/cetak bersama, dengan urutan PDF tetap dipertahankan sebagai lembar mandiri.
- Menambahkan migration incremental kolom `submitted_at` pada tabel claim dan mengisi data lama non-draft dari `created_at`; rollback mempertahankan timestamp agar tidak menghapus data historis.
- Pengiriman pertama sekarang merekam `submitted_at`; pengiriman ulang revisi mempertahankan tanggal awal tersebut.
- Mendokumentasikan acceptance criteria dan memperbarui checklist HRIS.

## Verifikasi

- Migration incremental `2026_11_13_000002_add_claim_submitted_at` berhasil dijalankan pada database Workspace; timestamp pengajuan lama non-draft di-backfill dari `created_at`.
- `php -l` berhasil untuk controller/service/model dan migration. Test suite tidak dijalankan.
- `php artisan view:cache` dan build Vite berhasil; PDF.js membuat bundle khusus halaman cetak dan worker lokal.
- `npm audit` melaporkan advisori pada dependency yang sudah ada (`axios`/`follow-redirects` dan `vite`/`laravel-vite-plugin`); dependency PDF.js baru tidak tercantum dalam advisori tersebut.

## Risiko / catatan

- Dialog cetak menggunakan fasilitas print browser; pengguna dapat memilih printer atau menyimpan hasil sebagai PDF.
- Nama perusahaan dipakai sebagai kop teks. Logo dan alamat perusahaan belum ditambahkan karena belum termasuk kebutuhan yang diberikan.
