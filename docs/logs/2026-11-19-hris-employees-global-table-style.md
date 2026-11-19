# Style global pada tabel Karyawan HRIS

## Perubahan

- Halaman `/workspace/hris/employees` memakai `.workspace-table-card` dan `.workspace-data-table`.
- Lebar enam kolom ditetapkan melalui `colgroup`; modifier `.workspace-data-table--employees` memberikan lebar minimum agar tabel dapat digulir pada layar sempit.
- Pagination memakai footer global `.workspace-list-pagination` yang sudah tersedia.
- Aksi per baris memakai dropdown tiga titik; CSS global dan positioning fixed memindahkan menu ke `document.body` agar tidak terpotong area scroll.
- Aturan global dan instruksi agen diarahkan ke class menu aksi yang dapat dipakai ulang pada tabel berikutnya.

## Verifikasi

- Memastikan route memakai view `hris-employees-enhanced` dan menerapkan class global pada tabel aktif.
- `git diff --check` bersih. Test/build tidak dijalankan.
