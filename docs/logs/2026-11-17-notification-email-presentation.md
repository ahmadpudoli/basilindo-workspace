# Tampilan email notifikasi Workspace

## Perubahan

- Template email notifikasi dibuat ulang dengan hierarki judul/pesan yang lebih jelas, header dan footer ringkas, serta tautan tindakan dan fallback yang mudah dikenali.
- Palet menggunakan latar netral dengan satu aksen biru yang tenang; layout memakai tabel dan style inline agar lebih konsisten di berbagai klien email.
- Subjek email baru memakai judul notifikasi agar penerima lebih mudah mengenali pesan dari kotak masuk.
- Requirement dan checklist diperbarui.

## Verifikasi

- Kompilasi Blade dan `git diff --check` dilakukan setelah perubahan.

## Risiko tersisa

- Tidak ada perubahan skema database. Test suite tidak dijalankan.
