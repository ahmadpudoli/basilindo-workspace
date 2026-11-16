# Requirement ringkasan lembur HRIS

## Tujuan

Memudahkan peninjauan pengajuan lembur pada cakupan perusahaan yang diizinkan, dengan filter yang konsisten dan tampilan responsif.

## Acceptance criteria

- Panel ringkasan menampilkan total pengajuan, pengajuan dalam proses, dan durasi lembur disetujui dalam satu panel compact berwarna.
- Filter mendukung pencarian server-side berdasarkan nama karyawan, NIP/nomor karyawan, dan nomor pengajuan.
- Filter perusahaan dan status mendukung pilihan berganda melalui dropdown dan hanya menawarkan pilihan yang berada dalam scope akses pengguna.
- Periode mulai default ke 1 Januari tahun berjalan dan periode akhir default ke hari ini; filter tanggal, perusahaan, status, kata kunci, dan pagination bekerja bersama.
- Tabel menampilkan karyawan/NIP, nomor pengajuan, perusahaan, realisasi (jumlah rincian dan rentang tanggal), jam, durasi, status, dan aksi dengan lebar kolom proporsional serta scroll horizontal pada layar kecil. Rentang filter tanggal mengevaluasi tanggal realisasi rincian lembur.
- Kolom aksi memakai tombol dropdown compact berikon tiga titik vertikal dengan menu Lihat detail.
- Pagination memakai pilihan 10/25/50/100, default 10, dan state filter/sort dipertahankan pada query string. Sortir tanggal dan durasi berjalan server-side dengan indikator arah.
- Scope perusahaan dan izin summary tetap diperiksa server-side.

