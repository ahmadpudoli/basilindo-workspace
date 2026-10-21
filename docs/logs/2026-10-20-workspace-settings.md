# Workspace Settings

## Perubahan

- Menambahkan modul Pengaturan dengan akses `super_admin` atau `access_settings`.
- Menambahkan pengaturan modul aktif global yang memengaruhi dashboard, launcher, dan shortcut modul.
- Mengembangkan pengelolaan user dengan role utama, direct permission tambahan, dan scope multi-company pada `company_user`.
- Menambahkan editor permission pada role sehingga akses dapat dikelola berbasis group maupun override langsung pada user.
- Menambahkan halaman Pengaturan, Modul Aktif, serta UI user access scope.
- Mempertahankan sidebar Pengaturan pada route legacy `/workspace/core/*`, termasuk halaman user dan role, tanpa memindahkan route yang sudah digunakan.
- Memperbaiki policy role dan user agar `super_admin` atau pemegang `access_settings` dapat mengelola halaman Pengaturan tanpa wajib memiliki permission granular legacy.
- Mengelompokkan permission role berdasarkan modul dengan kontrol pilih semua per kelompok agar konfigurasi role lebih mudah dipahami.
- Menyesuaikan form user agar mendukung banyak role, hanya menampilkan dan menerima perusahaan internal bertipe company, serta mengelompokkan permission tambahan berdasarkan modul.
- Menyederhanakan pemilihan role dan perusahaan pada form user menggunakan searchable multi-select native Bootstrap.
- Mengubah pemilihan role dan perusahaan menjadi dropdown multi-select yang lebih ringkas dengan pencarian di dalam daftar.
- Menampilkan nama/value pilihan langsung pada tombol dropdown, dengan tooltip untuk daftar lengkap saat teks terlalu panjang.
- Membuat panel permission tambahan per modul menjadi collapsible dan default collapsed agar form user lebih ringkas.
- Mengganti indikator collapsible dengan ikon chevron SVG modern yang beranimasi saat panel dibuka atau ditutup.
- Menyamakan lebar dropdown role dan perusahaan dengan parent serta menampilkan kode perusahaan pada nilai terpilih.
- Menghapus batas lebar CSS lama agar dropdown role dan perusahaan benar-benar memenuhi parent `col-12`.
- Memperbaiki status aktif sidebar agar route CRUD turunan seperti create, detail, dan edit tetap menandai menu parent yang sesuai.
- Menormalkan URL absolut menu menjadi path sebelum pencocokan agar menu aktif juga muncul pada halaman list seperti `/workspace/core/users`.
- Memperbaiki validasi unique email user agar menggunakan koneksi dan tabel model Core, bukan prefix tabel koneksi default.
- Menambahkan global loading overlay untuk submit form, konfirmasi delete/arsip, navigasi internal, dan pemuatan preview dokumen.
- Memperbaiki rule `exists` role, permission, dan perusahaan agar menggunakan koneksi/tabel model Core dan tidak lagi mencari tabel ber-prefix `ws_`.
- Mendokumentasikan batas employee satu perusahaan sebagai integration point untuk modul HRIS.

## Verifikasi

- PHP lint controller/service berhasil.
- `npm.cmd run build` berhasil.
- `php artisan view:cache` berhasil.
- Route Settings terdaftar setelah cache route dibersihkan.
- `git diff --check` berhasil.
- `php artisan view:cache` berhasil setelah penyesuaian konteks sidebar Settings.
- `php artisan test` berhasil pada suite yang tersedia di environment saat ini.

## Risiko tersisa

- Modul HRIS belum tersedia; relasi employee-to-company belum dibuat sampai domain HRIS disepakati.
