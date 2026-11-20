# Rekap lembur per karyawan — 2026-11-20

## Perubahan

- Menambahkan tab “Rekap per karyawan” di samping tampilan “Per pengajuan”.
- Memakai permission dan scope perusahaan ringkasan lembur yang sama, tanpa role atau permission baru.
- Mengagregasi jumlah pengajuan, jumlah disetujui, dan durasi disetujui per karyawan untuk periode/filter aktif.
- Menambahkan baris detail yang dapat diperluas berisi nomor pengajuan, rincian tanggal kerja, durasi, dan status.
- Mempertahankan filter, sorting, pilihan jumlah baris, dan pagination pada query string.
- Memperbarui requirement dan checklist.

## Verifikasi

- Belum menjalankan test atau build.

## Risiko tersisa

- Query dan tampilan tab belum diperiksa terhadap database/browser aplikasi.

## Perbaikan query agregasi

- PostgreSQL melaporkan ekspresi agregasi memakai nama tabel yang tidak mengikuti prefix koneksi HRIS (`hris_overtime_requests`).
- Nama tabel dan alias periode pada ekspresi agregasi kini dibangun dari konfigurasi koneksi/model.
- Verifikasi: lint PHP dan `git diff --check` dijalankan kembali; browser dan test suite belum dijalankan.

## Default periode rekap

- Tab “Rekap per karyawan” menggunakan tanggal 1 bulan berjalan sampai hari ini ketika parameter tanggal tidak ditentukan.
- Tab “Per pengajuan” mempertahankan default periode awal tahun yang sudah ada; tanggal yang dikirim melalui query string tetap dihormati pada kedua tab.
- Verifikasi: PHP lint dan `git diff --check`.
