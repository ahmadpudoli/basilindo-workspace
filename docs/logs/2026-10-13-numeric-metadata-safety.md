# Numeric metadata safety — 2026-10-13

## Perubahan

- Field metadata bertipe `numeric` hanya menerima bilangan bulat/integer.
- Field metadata bertipe `numeric_decimal` ditampilkan sebagai `Numeric Decimal`, memakai `step="any"`, dan menerima desimal titik atau koma.
- Nilai desimal dengan koma dinormalisasi ke format titik sebelum disimpan, contohnya `125000,75` menjadi `125000.75`.
- Validasi server membedakan integer dan decimal serta menolak nilai seperti `125000abc` atau `10.5` pada tipe `numeric`.
- Tampilan detail dokumen menggunakan JSON escaping untuk tag, ampersand, kutip, dan apostrof pada metadata.

## Verifikasi

- Desimal berhasil disimpan sebagai nilai canonical.
- Input teks non-numeric ditolak dengan `ValidationException`.
- Test upload metadata: 4 test lulus, 9 assertion.
