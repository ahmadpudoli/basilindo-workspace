# HRIS approval: pemisahan blok tindakan

## Perubahan

- Area tindakan pada halaman detail approval dipisahkan menjadi blok Setujui,
  Tolak, dan Minta revisi.
- Setiap blok memiliki judul, penjelasan singkat, field yang relevan, dan
  tombol aksi sendiri agar tidak terlihat sebagai satu form yang menyambung.

## Verifikasi

- Pemeriksaan struktur Blade pada halaman detail approval.
- `git diff --check`
