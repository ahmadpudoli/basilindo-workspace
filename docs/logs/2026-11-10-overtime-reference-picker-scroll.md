# Perbaikan scroll dropdown referensi lembur

## Perubahan

- Memisahkan area input pencarian dari area tabel pada dropdown Project dan Opportunity.
- Menjadikan wrapper tabel sebagai satu-satunya area overflow vertikal dan horizontal.
- Mempertahankan input pencarian tetap terlihat saat tabel discroll.

## Verifikasi

- php artisan view:cache

## Risiko tersisa

- Tinggi dropdown tetap mengikuti ruang viewport yang tersedia; pada layar sangat kecil tabel dapat tetap memerlukan scroll horizontal.
