# Pemisahan badge status referensi lembur

## Perubahan

- Memisahkan badge tipe opportunity menjadi Prospek atau Sales.
- Menambahkan klasifikasi badge Dibatalkan, Lost, dan Diabaikan berdasarkan code/name/flag closing status.
- Menampilkan Stage dan Closing sebagai badge terpisah.
- Menampilkan badge tipe/status, Stage, dan Closing pada panel Referensi terpilih.
- Mempertahankan label gabungan untuk pencarian dan snapshot data.

## Verifikasi

- PHP lint OvertimeWorkReferenceService
- php artisan view:cache
