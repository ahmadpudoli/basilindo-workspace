# Rendering PDF lampiran pada dokumen cetak reimbursement

Status: diterapkan

## Konteks

Dokumen cetak reimbursement perlu menyertakan semua halaman dari bukti PDF.
PDF viewer yang disematkan browser hanya memberi kontrol scroll dan perilaku
cetaknya tidak konsisten untuk setiap halaman dalam layout A4.

## Keputusan

- Gunakan PDF.js lokal (`pdfjs-dist`) pada halaman cetak untuk membaca jumlah
  halaman dan merender tiap halaman PDF menjadi canvas, lalu blob gambar lokal.
- Tampilkan satu halaman PDF pada satu lembar A4 dan hindari viewer PDF browser.
- Muat PDF melalui endpoint lampiran klaim privat yang sudah memeriksa relasi
  item, identitas pengguna, dan scope perusahaan. Tidak ada URL publik atau CDN.
- Tampilkan gambar langsung secara proporsional dan batasi tinggi cetaknya agar
  beberapa bukti dapat berbagi halaman.

## Alternatif

- Konversi PDF pada server memerlukan binary Poppler/Ghostscript atau library
  tambahan server yang tidak tersedia pada lingkungan ini.
- Menyematkan PDF viewer mempertahankan file asli, tetapi tidak memenuhi
  kebutuhan satu halaman PDF per halaman cetak dan lebih boros kertas.

## Konsekuensi

- Rendering berjalan di browser pengguna, menggunakan memori sesuai jumlah dan
  kompleksitas halaman PDF.
- Pengguna harus menunggu status pratinjau siap sebelum mencetak; jika PDF gagal
  dirender, halaman menampilkan tautan untuk membuka dan mencetak file terpisah.
- Asset PDF.js disimpan dan dibundel lokal, jadi tidak memerlukan CDN saat
  aplikasi dicetak melalui jaringan lokal/offline.
