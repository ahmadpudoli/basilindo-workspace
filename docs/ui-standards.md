# Standar UI Workspace

## Tabel dan tombol aksi

Semua tabel pada modul baru maupun modul yang sudah ada wajib mengikuti aturan berikut:

- Kolom `Aksi` diratakan ke kanan dan disejajarkan secara vertikal di tengah baris.
- Tombol aksi menggunakan ukuran `btn-sm` dan class global `.workspace-table-actions`.
- Tombol dan form aksi berada dalam satu kelompok dengan jarak horizontal konsisten 6px; form aksi tidak boleh membawa margin default yang membuat tombol bergeser.
- Tombol `Edit`, `Arsipkan`, `Hapus`, `Detail`, dan aksi lain harus memiliki tinggi kontrol yang sama serta tetap sejajar ketika jumlah tombol berubah.
- Daftar berbasis tabel wajib menyediakan pagination server-side dengan pilihan `10`, `25`, `50`, dan `100` baris, default `10`, serta mempertahankan filter dan sorting melalui query string.

Aturan ini menjadi acceptance criteria UI untuk implementasi fitur berikutnya dan acuan audit seluruh tabel yang sudah dibuat.

## Jarak kontrol filter

Grup tombol `Reset` dan `Terapkan` wajib memiliki jarak horizontal minimal 8px
dari field filter terakhir. Jarak ini diterapkan melalui komponen global agar
tombol tidak menempel pada input, sementara jarak antar tombol dalam satu grup
tetap mengikuti gaya segmented control yang konsisten.
Layout filter menggunakan `row g-2 align-items-end` dengan pembagian kolom
Bootstrap yang eksplisit, mengikuti pola halaman Tipe Dokumen. Hindari lebar
kolom action fixed yang menghilangkan gutter antar field.

## Format tanggal

Semua input tanggal pada Workspace wajib menampilkan format `dd/mm/yyyy`, tanpa
bergantung pada locale browser pengguna. Nilai yang dikirim ke server tetap
menggunakan format ISO `YYYY-MM-DD` agar validasi dan penyimpanan konsisten.
Input tanggal juga wajib menyediakan akses datepicker melalui tombol kalender
yang konsisten.
Select/dropdown dengan floating label wajib memiliki tinggi minimum 48px,
line-height dan padding vertikal yang cukup agar teks nilai tidak terpotong.
Saat input datepicker, dropdown, atau datetimepicker invalid, indikator error
harus berada di sebelah kiri ikon kalender/chevron; ikon kontrol tetap terlihat
dan tidak boleh saling menimpa.
Aturan ini berlaku untuk fitur baru dan field tanggal yang sudah tersedia.

## Workflow approval

Form konfigurasi workflow menggunakan single-select perusahaan. Satu perusahaan
hanya boleh memiliki satu konfigurasi aktif untuk setiap jenis workflow. Perubahan
dan penghapusan dilakukan melalui tabel konfigurasi; penghapusan memakai soft delete
agar histori pengaturan tetap tersedia dan tidak mengubah catatan approval yang telah
dibuat.
