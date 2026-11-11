# Jenis hari pada rincian lembur

## Perubahan

- Menambahkan field per rincian lembur untuk memilih Hari Kerja atau Hari Libur.
- Menyimpan nilai internal workday/holiday pada tabel overtime_request_entries.
- Menambahkan validasi server-side dan default workday untuk kompatibilitas data lama.
- Menampilkan jenis hari pada detail lembur dan detail approval.
- Memadatkan tampilan rincian: tanggal, jenis hari, mulai, dan selesai berada satu baris pada desktop; uraian tetap full-width.
- Menambahkan fallback responsive agar seluruh field tersusun vertikal pada layar kecil.
- Menyelaraskan jenis hari dengan field tanggal dan waktu dalam satu baris compact.
- Mengubah input jam menjadi format teks `HH:mm` 24 jam dengan formatter input dan validasi pola.
- Menyusun ulang field menjadi tanggal, mulai, selesai, lalu jenis hari; mengaktifkan kembali native time picker dengan interval satu menit dan locale 24 jam.
- Mengurangi tinggi pilihan jenis hari agar tidak bertumpuk dengan label.
- Mengganti picker native yang mengikuti locale OS dengan picker custom 24 jam: jam `00–23` dan menit `00–59`, tanpa AM/PM.
- Menambahkan jarak antara label dan tombol jenis hari serta warna merah khusus untuk opsi Hari Libur.
- Menyesuaikan jarak tombol dengan transform visual agar label jenis hari tetap sejajar dan tidak ikut bergeser.
- Merapikan segmented control jenis hari dengan ukuran konsisten dan ikon check pada pilihan aktif.
- Memperbaiki radio jenis hari pada rincian tambahan dengan membuat ID label unik per nilai radio.
- Meratakan posisi kiri label jenis hari dengan tombol Hari Kerja.
- Menambahkan validasi client-side berbasis JavaScript dan pesan server-side terlokalisasi; input lama tanpa day_type dinormalisasi sebagai workday.
- Memperbaiki validasi tanggal client-side agar tidak terpengaruh pergeseran timezone saat membandingkan tanggal lokal.
- Menetapkan satu rincian untuk satu tanggal kalender; jam selesai wajib lebih besar dari jam mulai dan lembur lintas tengah malam harus memakai rincian tanggal berikutnya.
- Mendesain ulang halaman detail lembur agar lebih compact, menyembunyikan panel alasan/konteks, dan memperjelas hierarchy informasi serta tabel realisasi.
- Menata ulang halaman detail menjadi layout utama kiri dan sidebar workflow kanan yang lebih sempit, dengan responsive fallback satu kolom.
- Menampilkan HR approver aktif berdasarkan perusahaan karyawan pada form pengajuan dan revisi lembur.
- Memperbaiki penempatan grid halaman detail agar panel realisasi langsung berada di bawah informasi utama tanpa ruang kosong akibat sidebar workflow.
- Menyesuaikan badge Hari Libur pada tabel detail lembur menjadi latar merah dengan teks putih.
- Menyamakan badge Hari Libur pada halaman detail approval manager menjadi latar merah dengan teks putih.
- Memperbaiki routing notifikasi resubmit: revisi administrasi dari HR kembali ke HR yang meminta revisi, sedangkan revisi substantif kembali ke Atasan; redaksi tidak lagi menyebut pengajuan sebagai pengajuan baru.

## Verifikasi

- PHP lint model, Form Request, dan service lembur.
- php artisan view:cache
- Migration incremental 2026_11_10_000003_add_overtime_entry_day_type berhasil dijalankan.
