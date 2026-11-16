# Requirement pratinjau A4 cetakan reimbursement

## Tujuan

Pratinjau pada halaman cetak reimbursement harus terlihat seperti lembar kertas A4, bukan kartu yang tingginya mengikuti isi lampiran.

## Acceptance criteria

- Halaman ringkasan, lampiran gambar, dan setiap halaman PDF memiliki proporsi A4 portrait (210:297) di layar.
- Setiap lampiran yang dimulai sebagai halaman baru tetap menempati satu area A4 pada hasil cetak.
- Ukuran dan margin cetak tetap mengikuti A4 portrait dengan margin 14 mm.
- Foto bukti tetap proporsional dan muat dalam area lampiran; isi PDF tetap dirender ke masing-masing lembar.
- Tampilan layar kecil mempertahankan rasio A4 dan tidak mengubah halaman menjadi kartu setinggi kontennya.

## Implementasi

Gunakan `aspect-ratio: 210 / 297` untuk lembar pada layar, lalu dimensi area konten 269 mm (297 mm dikurangi margin atas/bawah 14 mm) untuk lembar cetak. Isi ringkasan dipadatkan agar blok persetujuan tetap muat pada halaman pertama.
