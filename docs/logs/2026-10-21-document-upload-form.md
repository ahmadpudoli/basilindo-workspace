# Penyesuaian form upload dokumen

## Perubahan

- Menata file upload dan deskripsi dalam dua panel berdampingan.
- Menambahkan thumbnail untuk gambar dan icon file untuk PDF atau format lain.
- Memadatkan area preview dan editor menjadi tinggi 75px.
- Menata kontrol file dan preview menjadi dua kolom compact dengan tinggi yang sama.
- Menambahkan font icon Summernote yang dibutuhkan oleh toolbar.
- Menggunakan Summernote 0.8.18 dari asset project referensi sebagai editor WYSIWYG lokal.
- Memindahkan input judul ke setelah pasangan file dan deskripsi.
- Menambahkan requirement, acceptance criteria, dan checklist.

## Verifikasi

- Asset jQuery dan Summernote disalin ke `workspace/public/vendor/summernote/`.
- `npm run build` dijalankan setelah perubahan frontend.
- Field `description` tetap menggunakan nama input yang diproses backend.

## Risiko tersisa

- Validasi backend saat ini tetap membatasi upload pada PDF, JPG, dan PNG sesuai kontrak existing.
