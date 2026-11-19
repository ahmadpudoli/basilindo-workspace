# Floating label form Data Pembayaran Karyawan

## Perubahan

- Mengganti wrapper Bootstrap `.form-floating` menjadi `.workspace-floating-field`
  dan label `.form-label` pada input nama bank, pemilik, dan nomor rekening.
- Menambahkan aturan agen agar field form floating label berikutnya memakai
  komponen global Workspace untuk textbox, select, tanggal, angka, dan textarea.

## Verifikasi

- Memeriksa bahwa markup form mengikuti struktur komponen floating label yang
  digunakan form HRIS lain.
- Test otomatis tidak dijalankan.
