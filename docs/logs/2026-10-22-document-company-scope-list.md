# Perbaikan scope perusahaan pada daftar dokumen

## Perubahan

- Memperbaiki `DocumentCompanyScope` agar `super_admin` tidak dibatasi oleh assignment perusahaan yang kebetulan dimilikinya.
- Menambahkan filter `Perusahaan operasional` pada daftar dokumen.
- Opsi filter dan opsi perusahaan pada form upload memakai `Company::operationalFor($user)`.
- Menolak create/update dokumen jika perusahaan operasional yang dikirim berada di luar scope pengguna.
- Daftar dokumen sekarang menyediakan upload action, multi-select perusahaan operasional, dropdown tahun dari tanggal dokumen, pilihan jumlah baris, dan pagination server-side dengan tabel compact.
- Menu navigasi upload terpisah dihapus karena akses upload tersedia langsung dari header daftar dokumen.

## Verifikasi

- Query database aktual menemukan dokumen perusahaan ID 4 dan sebelumnya mengembalikan 0 hasil untuk user super admin karena scope salah; setelah perbaikan scope mengembalikan 1 hasil.
- `php artisan view:cache` dan pemeriksaan syntax PHP berhasil.

## Data

- Tidak ada migration, reset database, atau penghapusan data.

## Sorting tabel dokumen

- Menambahkan sorting server-side pada kolom judul, perusahaan, jenis, tanggal, dan status.
- Link sorting mempertahankan filter, jumlah baris, dan pagination melalui `withQueryString()`.
- Menambahkan indikator arah sort pada header tabel.
- Verifikasi: view cache, build frontend, dan pemeriksaan whitespace.

## Standardisasi panel filter

- Menyamakan tinggi input, select, dan multiselect pada panel filter daftar.
- Menyatukan tombol `Reset` dan `Terapkan` dalam satu grup aksi pada daftar perusahaan, vendor, dokumen, dan tipe dokumen.
- Menambahkan aturan ini ke panduan pagination dan UI Workspace.
- Memperkecil kolom Tahun dan Jumlah pada filter dokumen serta memaksa tinggi multiselect perusahaan operasional tetap sama dengan kontrol filter lainnya.
- Menata ulang baris filter dokumen dengan grid khusus agar tombol Reset/Terapkan lebih lebar dan rata kanan.
- Mengembalikan elemen field filter ke struktur `col-*` agar aturan floating label global tetap berlaku konsisten.
- Menyamakan ruang vertikal wrapper multiselect filter dengan input biasa agar Perusahaan operasional sejajar.
- Menata seluruh field filter perusahaan dalam grid proporsional, meratakan grup aksi ke kanan, dan menyamakan background Terapkan dengan tombol primary Workspace.
- Menambah porsi lebar Jenis dan Klasifikasi serta menyelaraskan label floating Peran dengan wrapper multiselect.
- Memperbesar lagi porsi Jenis dan Klasifikasi serta mengunci offset akhir label Peran agar tidak tertimpa rule global.
- Mengurangi lebar filter Jenis dan Klasifikasi pada daftar perusahaan untuk memberi ruang proporsional pada tombol Terapkan.
- Mengunci field Peran dengan class khusus agar floating label tetap sejajar dan menambah lebar Jenis serta Klasifikasi.
- Menambahkan override final setelah seluruh rule floating label global agar posisi label Peran tidak tertimpa kembali.
- Merombak struktur filter perusahaan agar seluruh field memakai `.workspace-floating-field` global seperti form create/edit, termasuk multiselect Peran.
- Menyetarakan lagi posisi vertikal label Peran dan mengurangi lebar grup aksi agar tombol Terapkan tetap compact.
- Menghapus padding dan override khusus filter Peran agar seluruh field kembali mengikuti CSS global `.workspace-floating-field`.
- Menyamakan tinggi dan padding dropdown multiselect floating secara global menjadi 40px agar field Peran sejajar dengan input/select.
- Menyamakan tinggi wrapper multiselect floating menjadi 40px untuk menghilangkan ruang bawah tambahan pada field Peran.
- Mengganti transform bertingkat label Peran dengan posisi absolut sederhana pada container field agar label menempel pada garis input.
- Menurunkan posisi akhir label Peran agar bagian bawah label tepat pada garis atas field.
- Menyamakan filter Vendor dengan standar global: `.workspace-floating-field`, kontrol sebelum label, ukuran field konsisten, serta grup Reset/Terapkan dengan Terapkan sebagai tombol utama.
- Memperbaiki struktur Vendor agar setiap `.workspace-floating-field` berada di dalam wrapper `col-*` seperti filter Perusahaan; ini memastikan label floating tidak keluar atau terpotong dan tinggi field mengikuti CSS global.
- Menyetarakan tombol aksi tabel dengan flex alignment, gap global, dan form inline-flex agar Edit dan Arsipkan memiliki tinggi serta posisi vertikal yang sama.
- Mengoptimalkan daftar Vendor dengan multiselect perusahaan operasional yang dibatasi scope pengguna, sorting whitelist pada kolom tabel, pagination server-side, query string persisten, dan tabel compact `table-sm`.
- Menyesuaikan grid filter Vendor agar field tetap sejajar pada desktop dan baru menjadi dua kolom pada layar kecil, tanpa terpengaruh lebar default wrapper Bootstrap.
- Mengunci ulang grid Vendor setelah breakpoint global agar lima kontrol filter tidak turun menjadi beberapa baris pada viewport desktop yang diperbesar browser; hanya viewport sangat kecil yang memakai satu kolom.
- Mengganti layout filter Vendor menjadi flex satu baris yang eksplisit agar aturan grid dan lebar `.col-md-*` tidak lagi menurunkan field Status, Jumlah, atau tombol aksi.
- Menjadikan struktur filter halaman Perusahaan sebagai standar fitur daftar: form global, row Bootstrap, wrapper `col-*`, `.workspace-floating-field`, dan layout kolom resource yang terpisah dari markup field.
- Mengembalikan Vendor ke layout grid resource setelah flex sementara terbukti mengganggu konsistensi pola halaman Perusahaan.
