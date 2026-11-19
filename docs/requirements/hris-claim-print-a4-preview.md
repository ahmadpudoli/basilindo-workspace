# Requirement pratinjau ukuran kertas cetakan reimbursement

## Tujuan

Pratinjau pada halaman cetak reimbursement harus terlihat seperti lembar kertas sesuai ukuran yang dipilih, bukan kartu yang tingginya mengikuti isi lampiran.

## Acceptance criteria

- Dropdown ukuran kertas berada di samping tombol cetak dan menyediakan A4, Letter, Legal, serta A3 portrait; default-nya A4.
- Halaman ringkasan, lampiran gambar, dan setiap halaman PDF langsung mengikuti rasio ukuran kertas yang dipilih di layar.
- Rincian biaya dan panel persetujuan mengisi ruang halaman yang tersedia secara berurutan; panel hanya berpindah ke halaman berikutnya bila tidak muat. Lampiran bukti selalu dimulai pada halaman baru.
- Rincian biaya dan tanda tangan/persetujuan memiliki panel visual mandiri pada lembar rincian.
- Metadata cetak menampilkan nama pengguna pencetak dan waktu cetak sesuai zona waktu aplikasi.
- Setiap lampiran yang dimulai sebagai halaman baru tetap menempati satu area A4 pada hasil cetak.
- Ukuran cetak mengikuti pilihan kertas dengan margin 14 mm.
- Foto bukti tetap proporsional dan muat dalam area lampiran; isi PDF tetap dirender ke masing-masing lembar.
- Tampilan layar kecil mempertahankan rasio A4 dan tidak mengubah halaman menjadi kartu setinggi kontennya.

## Implementasi

Gunakan ukuran A4 sebagai default dan perbarui lebar serta tinggi eksplisit setiap lembar preview (termasuk PDF), tinggi area cetak, serta descriptor `@page size` ketika pilihan atau ukuran viewport berubah. Area konten cetak dihitung dari tinggi kertas dikurangi margin atas/bawah 14 mm. Isi ringkasan dipadatkan agar blok persetujuan tetap muat pada halaman pertama.
