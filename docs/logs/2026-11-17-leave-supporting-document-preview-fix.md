# Perbaikan preview dokumen pendukung cuti/sakit

## Perubahan

- Directive `@include` pada cabang gambar dipisahkan dari `@else` agar Blade mengompilasi partial zoom dengan benar.
- Perbaikan diterapkan pada form edit draft/revisi, form revisi modern, dan detail cuti/sakit yang memakai pola preview yang sama.
- Checklist fitur diperbarui.

## Verifikasi

- Kompilasi Blade dan `git diff --check` dilakukan setelah perubahan.

## Risiko tersisa

- Tidak ada perubahan data atau skema. Test suite tidak dijalankan.
