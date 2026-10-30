# Perbaikan ParseError detail persetujuan HRIS

## Perubahan

- Memecah directive Blade inline pada blok dokumen pendukung dan timeline menjadi struktur multiline.
- Menjaga alur preview/unduh dokumen, timeline alasan, dan modal tindakan approval tetap sama.

## Verifikasi

- Controller HRIS terkait lolos `php -l`.
- Blade berhasil dikompilasi menggunakan `BladeCompiler` tanpa ParseError; evaluasi penuh view membutuhkan runtime view context Laravel.
- `git diff --check` berhasil.
