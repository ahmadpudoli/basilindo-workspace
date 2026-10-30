# Unduh dokumen pendukung detail cuti/sakit

## Perubahan

- Menambahkan dialog preview untuk PDF dan gambar pada detail cuti/sakit bila object private masih tersedia.
- File selain PDF/gambar tetap menggunakan endpoint streamed download yang dilindungi policy `view` dan private disk HRIS.

## Verifikasi

- PHP lint dan route check dijalankan setelah implementasi.
