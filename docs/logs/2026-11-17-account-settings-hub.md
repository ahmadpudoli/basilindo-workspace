# Pengaturan Akun Workspace

## Perubahan

- Menu profil diringkas menjadi satu tautan **Pengaturan Akun**.
- Pengaturan Akun kini membuka **Ringkasan Akun** terlebih dahulu, berisi profil, status verifikasi, peran Workspace, dan daftar scope perusahaan user; Super Admin ditandai memiliki akses seluruh perusahaan.
- Navigasi internal menyediakan **Ringkasan Akun**, **Ubah Password**, dan **Preferensi Notifikasi**, memakai warna dan tata letak yang membedakannya dari navigasi modul.
- Form password dan preferensi email dipindahkan ke partial yang digunakan di halaman bersama.
- Di layar mobile, sidebar bagian tersembunyi dan dibuka melalui tombol sebagai offcanvas; desktop mempertahankan sidebar tetap terbuka.
- Sidebar desktop mengisi kolom 400 px dengan tinggi mengikuti konten; offcanvas mobile tetap dibatasi hingga 360 px, dan email akun dapat membungkus baris agar tidak terpotong.
- Pesan status global layout disembunyikan khusus di halaman pengaturan akun agar feedback sukses hanya muncul sekali di area konten.
- URL pengaturan lama tetap dipertahankan sebagai redirect ke bagian terkait; submit kedua form kembali ke bagian yang sama.
- Requirement dan checklist diperbarui.

## Verifikasi

- Pemeriksaan sintaks PHP, kompilasi Blade, dan `git diff --check` dilakukan setelah perubahan.

## Risiko tersisa

- Tidak ada perubahan skema database. Test suite tidak dijalankan.
