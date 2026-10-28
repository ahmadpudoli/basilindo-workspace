# Standar format tanggal global

Tanggal: 2026-10-28

## Perubahan

- Menambahkan enhancer global untuk seluruh `input[type="date"]` agar tampil
  dalam format `dd/mm/yyyy` dan tidak mengikuti format locale browser.
- Nilai tampilan dikonversi kembali ke ISO `YYYY-MM-DD` melalui hidden input
  sebelum dikirim ke server.
- Menambahkan input mask sederhana untuk slash tanggal dan validasi tanggal
  kalender yang valid.
- Menambahkan tombol kalender global yang membuka datepicker native browser,
  termasuk pada tanggal dokumen di halaman Upload Dokumen.
- Memperbaiki MutationObserver agar input datepicker internal tidak diproses
  ulang tanpa henti dan menyebabkan browser mendeteksi script lambat.
- Memisahkan kontrol datepicker dari baris tanggal cuti agar ikon kalender tidak
  menumpuk di atas tombol `Hapus`.
- Menyesuaikan form pengajuan cuti/sakit agar membaca field tanggal global.

## Verifikasi

- Test validasi server-side cuti/sakit tetap berhasil.
- Perubahan aturan didokumentasikan sebagai standar UI global untuk fitur baru
  dan fitur yang sudah ada.
- Syntax check JavaScript berhasil dijalankan.
- Indikator error pada datepicker, dropdown, dan datetimepicker diposisikan di
  sebelah kiri ikon kalender/chevron agar tidak bertumpuk.
- Memperbaiki overlay loading agar tidak aktif ketika submit dibatalkan oleh
  validasi client-side pada form pengajuan cuti/sakit.
