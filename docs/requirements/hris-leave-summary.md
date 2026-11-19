# Requirement ringkasan cuti dan sakit HRIS

## Tujuan

Menyediakan ringkasan pengajuan cuti dan sakit dalam scope perusahaan yang mudah dicari dan ditinjau di desktop maupun perangkat mobile.

## Acceptance criteria

- Pencarian kata kunci server-side mencari nama karyawan, NIP/nomor karyawan, dan nomor pengajuan serta mempertahankan kata kunci melalui query string dan pagination.
- Tabel menampilkan nama/NIP, nomor pengajuan, jenis, tanggal pengajuan, jumlah hari, status, dan aksi.
- Lebar kolom ditetapkan proporsional; tanggal dapat membungkus, status tetap terbaca, dan tabel dapat digulir horizontal pada layar kecil tanpa isi kolom saling menimpa.
- Kolom Aksi hanya menampilkan tombol dropdown compact berikon tiga titik vertikal. Menu menyediakan detail dan pratinjau dokumen bila tersedia, menggunakan pola posisi viewport agar tidak terpotong tabel, dan mempertahankan authorization yang berlaku.
- Metrik Total pengajuan, Masih diproses, dan Hari disetujui disajikan dalam satu panel ringkasan modern berwarna dengan pemisah, bukan kartu terpisah.
- Scope data ringkasan tetap dibatasi berdasarkan permission dan perusahaan yang dapat diakses pengguna.
- Filter jenis cuti dan status mendukung banyak pilihan melalui dropdown, menerapkan seluruh pilihan di server, serta mempertahankan pilihan melalui query string dan pagination. Filter tahun, kata kunci, dan jumlah baris tetap bekerja bersama.
- Detail cuti/sakit menempatkan dokumen pendukung di panel tanggal dan alasan, tepat setelah alasan pengajuan; pratinjau/unduh tetap mengikuti authorization yang berlaku.
- Panel tanggal detail menampilkan identitas pemohon, NIP/nomor karyawan, perusahaan, dan unit/jabatan seperti informasi pemohon pada detail approval.
- Pada desktop, panel tanggal menggunakan 7/12 lebar baris dan timeline approval 5/12; pada layar kecil kedua panel tetap menumpuk.
- Timeline approval detail cuti/sakit menggunakan panel collapsible yang terbuka secara default, dengan kontrol berlabel dan state aksesibel.
- Detail memakai ringkasan dan panel modern yang compact dengan hierarki visual yang konsisten pada desktop dan layar kecil; tanggal memakai kotak berlatar lembut, sementara alasan dan dokumen tetap disusun datar dengan divider halus.
- Tombol Ajukan cuti/sakit pada daftar menggunakan ikon tambah, warna aksen lembut, serta state hover dan focus yang jelas tanpa mengubah tujuan atau pemeriksaan permission.

## Keputusan

Pencarian memakai PostgreSQL `ILIKE` untuk nama, nomor karyawan, dan nomor pengajuan. Panel metrik tetap mengikuti definisi agregasi yang sudah ada; kata kunci menyaring baris daftar.
