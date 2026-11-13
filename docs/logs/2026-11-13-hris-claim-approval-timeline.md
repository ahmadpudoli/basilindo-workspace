# Timeline pada detail approval reimbursement

## Perubahan

- Detail approval claim memuat timeline pengajuan, keputusan, permintaan revisi, dan pengiriman ulang di accordion tepat setelah Rincian biaya; accordion tertutup secara default.
- Event, identitas pelaku, dan komentar dimuat/ditampilkan bagi pengguna yang lolos otorisasi halaman detail approval.
- Bagian Dokumen pendukung umum disembunyikan pada claim karena bukti sudah ditautkan ke setiap rincian biaya. Halaman cuti dan lembur tetap memakai bagian tersebut.
- Requirement dan checklist diperbarui.

## Tindak lanjut

- Atas permintaan pengguna, timeline tidak lagi menggunakan permission audit untuk menentukan tampil/tidaknya panel. Otorisasi halaman approval tetap membatasi akses ke detail pengajuan.
- Migration permission audit inbox sempat dijalankan sebelum keputusan ini; grant tersebut tidak lagi menjadi syarat tampilan timeline.
- Header accordion diperbarui dengan ikon riwayat berbasis SVG, jarak dan tinggi yang lebih ringkas, serta gradasi biru-ungu yang lembut.
- Timestamp event approval sebelumnya memakai default PostgreSQL (UTC), sementara timestamp model pengajuan dibuat dalam `Asia/Jakarta`. Event terbaca lebih awal tujuh jam dan mengacaukan posisi awal pengajuan pada timeline.
- Model `ApprovalEvent` kini membaca timestamp database sebagai UTC lalu mengonversinya ke timezone aplikasi. Seluruh jalur keputusan, revisi, pengiriman ulang, dan pembayaran menulis timestamp UTC secara eksplisit.
- Modal preview bukti pada detail approval Finance belum memakai markup zoom yang dikenali oleh handler gambar bersama. Toolbar zoom kini ditambahkan pada viewer approval; handler hanya menampilkannya jika isi iframe berupa gambar.

## Verifikasi

- Meninjau authorization detail approval, policy audit claim, relasi approval event, serta kondisi Blade untuk claim dan non-claim.
- Memastikan modal approval menggunakan handler zoom bersama melalui `data-claim-image-viewer`; toolbar hanya ditampilkan ketika iframe memuat elemen gambar.
- Audit menyeluruh menemukan modal generik dokumen serta modal lampiran cuti/approval lain masih menampilkan gambar tanpa handler zoom. Semua modal tersebut kini memakai viewer gambar bersama.
- Build Vite berhasil dan Blade templates berhasil dikompilasi dengan `php artisan view:cache`; pemeriksaan visual browser belum dilakukan.
- Migration penyelarasan permission audit sempat dijalankan pada database lokal; timeline kini tetap dirender tanpa bergantung pada grant tersebut.
- Test otomatis tidak dijalankan.

## Risiko tersisa

- Verifikasi visual pada browser lokal belum dilakukan setelah perubahan terakhir.
