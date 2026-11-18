# Fitur ubah password akun Workspace

## Tujuan

Pengguna yang sedang masuk melihat ringkasan profil dan scope perusahaan, lalu mengelola keamanan akun dan preferensi notifikasi dari satu halaman pengaturan akun Workspace.

## Acceptance criteria

- Menu profil hanya menampilkan satu tautan Pengaturan Akun untuk membuka halaman pengaturan umum.
- Halaman Pengaturan Akun memiliki navigasi internal yang berbeda secara visual dari navigasi modul, dengan tiga bagian: Ringkasan Akun, Ubah Password, dan Preferensi Notifikasi.
- Ringkasan Akun menjadi halaman awal dan menampilkan identitas, email/verifikasi, peran Workspace, serta perusahaan yang berada dalam scope akun.
- Daftar scope perusahaan memakai relasi perusahaan user; Super Admin ditandai memiliki akses seluruh perusahaan. Akun tanpa scope perusahaan mendapat pesan yang menjelaskan cara meminta akses.
- Sidebar desktop mengisi kolom selebar 400 px dan tingginya mengikuti konten; panel offcanvas mobile dapat menggunakan lebar hingga 360 px tanpa melampaui viewport.
- Feedback berhasil pada Pengaturan Akun hanya ditampilkan satu kali di dalam konten halaman.
- Pada perangkat mobile, sidebar bagian tersembunyi dari layout utama dan dibuka sebagai offcanvas melalui tombol menu, mengikuti pola sidebar Workspace.
- Pengaturan akun dan preferensi notifikasi hanya dapat diakses setelah login; URL lama tetap mengarahkan ke bagian yang sesuai.
- Pengguna harus memasukkan password saat ini yang valid sebelum password diganti.
- Password baru minimal 8 karakter, harus dikonfirmasi, dan harus berbeda dari password saat ini.
- Password disimpan menggunakan hash melalui cast model User; password mentah tidak dicatat.
- Setelah berhasil, session aktif diperbarui, session database lain milik pengguna dicabut, dan aktivitas dicatat tanpa data password.
- Form menyediakan pesan validasi yang jelas dan mendukung password manager/autofill.
- Preferensi notifikasi email dapat diubah dan disimpan dari bagian Preferensi Notifikasi pada halaman yang sama.
