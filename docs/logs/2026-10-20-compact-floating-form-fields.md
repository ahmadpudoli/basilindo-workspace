# Compact floating-label form fields

## Perubahan

- Menambahkan pola floating-label compact pada design system Workspace.
- Menambahkan enhancement JavaScript yang mengonversi pasangan `label.form-label` dengan input/select/textarea menjadi Bootstrap `form-floating` tanpa mengubah nama field, validation, atau binding Livewire.
- Menambahkan ukuran field yang lebih compact dan proporsional, focus state, invalid state, serta dukungan dark theme.
- File upload dikecualikan dari transformasi karena memerlukan kontrol file khusus.
- Memperbaiki posisi label agar berada di atas garis border dengan background cutout seperti template, bukan berada di dalam kotak field.
- Merapikan grid form dokumen dengan menghilangkan row bersarang pada komponen Livewire metadata dan menyamakan tinggi file input.
- Mengganti wrapper Bootstrap `form-floating` dengan wrapper custom agar tinggi input/select/textarea konsisten dan menambahkan transisi label saat focus.
- Menyetel ulang padding vertikal nilai agar berada di tengah field dengan sedikit offset ke bawah yang konsisten.
- Menormalkan tinggi, line-height, dan tombol native pada input upload agar tidak menyisakan ruang kosong di bagian bawah.
- Mengunci alignment item pada grid form ke posisi atas yang sama dan menghapus margin negatif tombol file native agar field upload sejajar.
- Menurunkan kembali ukuran kontrol file ke 40px agar tetap compact dan tidak lebih besar dari input standar.
- Menyamakan gutter vertikal field pertama dari komponen Livewire dengan child langsung Bootstrap row agar kolom kiri dan kanan sejajar.
- Menyamakan padding horizontal dan box sizing child Livewire dengan kolom Bootstrap agar batas kiri-kanan, lebar, dan jarak antar-field konsisten.

## Verifikasi

- `npm.cmd run build` berhasil.
- `php artisan view:cache` berhasil.
- Test view dan authentication terkait berhasil: 5 test, 8 assertion.
- Build frontend dan cache Blade berhasil setelah perbaikan visual.
- Test form dokumen berhasil: 10 test, 20 assertion.
- Build frontend terbaru dan cache Blade berhasil setelah normalisasi input upload.
- Build frontend terbaru dan cache Blade berhasil setelah penyesuaian alignment grid dan file control.
- Build frontend terbaru dan cache Blade berhasil setelah penyesuaian ukuran compact file control.
- Build frontend terbaru dan cache Blade berhasil setelah perbaikan alignment gutter Livewire.
- Build frontend terbaru dan cache Blade berhasil setelah normalisasi ukuran kolom dan padding form.
