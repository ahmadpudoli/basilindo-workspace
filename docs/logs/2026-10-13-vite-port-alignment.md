# Penyelarasan port Vite — 2026-10-13

## Masalah

Browser mencoba koneksi WebSocket HMR ke port `5174`, sedangkan development asset Workspace seharusnya berjalan pada `5173`. Vite belum memakai `strictPort`, sehingga dapat berpindah otomatis ketika `5173` sedang terpakai.

## Perubahan

- Vite Workspace dikunci pada host `127.0.0.1` dan port `5173`.
- HMR dikunci pada host dan port yang sama.
- `strictPort: true` mencegah Vite diam-diam berpindah ke `5174` atau port lain.

## Petunjuk restart

1. Hentikan proses Vite lama.
2. Jalankan `npm run dev` dari `app/workspace`.
3. Pastikan terminal menampilkan `http://127.0.0.1:5173`.
4. Buka ulang halaman atau lakukan `Ctrl+F5`.

Jika muncul pesan port 5173 sedang digunakan, tutup proses Node/Vite lama terlebih dahulu; jangan menjalankan Vite kedua pada project yang sama.
