# Tautan detail pengajuan pada notifikasi — 2026-11-17

## Perubahan

- Menambahkan `NotificationLinkResolver` bersama untuk menentukan tujuan notifikasi HRIS berdasarkan jenis dan ID pengajuan.
- Notifikasi approval aktif diarahkan ke detail approval sesuai tahap; approval yang sudah tidak aktif mengarah ke daftar approval.
- Notifikasi non-approval diarahkan ke detail cuti/sakit, lembur, atau klaim yang tepat.
- Menambahkan tombol dan tautan cadangan pada body Blade email generik. URL yang sama disertakan pada payload Web Push.
- Halaman detail notifikasi aplikasi menggunakan resolver yang sama agar label dan tujuan konsisten.

## Verifikasi

- Pemeriksaan sintaks PHP dan diff whitespace dilakukan setelah perubahan.
- Test suite tidak dijalankan.

## Risiko/asumsi

- Email menggunakan absolute URL dari `APP_URL`; setiap environment deployment harus menetapkan domain Workspace yang benar.
- Endpoint detail tetap memeriksa autentikasi dan authorization yang sudah berlaku.
