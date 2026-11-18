# Perbaikan loading saat mengunduh dokumen pendukung

## Perubahan

- Indikator navigasi global kini melewati tautan unduhan dengan atribut `download`, tautan unduh eksplisit, dan tautan pada aksi dokumen pendukung.
- Unduhan tidak lagi dianggap navigasi halaman, sehingga overlay tidak tertinggal setelah browser menerima file.
- Requirement dan checklist HRIS diperbarui.

## Verifikasi

- Pemeriksaan sintaks JavaScript dan `git diff --check` dilakukan setelah perubahan.

## Risiko tersisa

- Tidak ada perubahan backend atau skema database.
