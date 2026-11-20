# Requirement ringkasan lembur HRIS

## Tujuan

Memudahkan peninjauan pengajuan lembur pada cakupan perusahaan yang diizinkan, dengan filter yang konsisten dan tampilan responsif.

## Acceptance criteria

- Panel ringkasan menampilkan total pengajuan, pengajuan dalam proses, dan durasi lembur disetujui dalam satu panel compact berwarna; metrik ditampilkan datar dengan pemisah halus tanpa kartu bersarang atau bingkai warna per status.
- Tinggi panel desktop dan mobile dijaga compact dengan padding dan tinggi minimum metrik yang ringkas.
- Header ikhtisar menempatkan tanggal dan judul pada satu baris ringkas, tanpa paragraf pengantar yang mengulang fungsi filter.
- Latar panel memakai token global `--workspace-kpi-panel-background` yang sama dengan panel KPI Workspace; teks navy dan pemisah tetap netral.
- Pada tata letak mobile dua kolom, pemisah vertikal dan horizontal harus terlihat jelas di antara metrik.
- Pada mobile, panel dapat diciutkan/dibuka melalui kontrol ringkasan dan terbuka secara default; di desktop panel tetap tampil tanpa kontrol tambahan.
- Filter mendukung pencarian server-side berdasarkan nama karyawan, NIP/nomor karyawan, dan nomor pengajuan.
- Filter perusahaan dan status mendukung pilihan berganda melalui dropdown dan hanya menawarkan pilihan yang berada dalam scope akses pengguna.
- Filter riwayat approval menyediakan “Disetujui oleh saya” dan “Ditolak oleh saya”, masing-masing hanya menampilkan pengajuan lembur yang memiliki event `approve` atau `reject` oleh pengguna login pada tahap atasan atau HR; filter ini dapat dipakai bersama filter tanggal, perusahaan, status, kata kunci, sorting, dan pagination.
- Periode mulai default ke 1 Januari tahun berjalan dan periode akhir default ke hari ini; filter tanggal, perusahaan, status, kata kunci, dan pagination bekerja bersama.
- Tabel menampilkan karyawan/NIP, nomor pengajuan, perusahaan, realisasi (jumlah rincian dan rentang tanggal), jam, durasi, status, dan aksi dengan lebar kolom proporsional serta scroll horizontal pada layar kecil. Rentang filter tanggal mengevaluasi tanggal realisasi rincian lembur.
- Kolom aksi memakai tombol dropdown compact berikon tiga titik vertikal dengan menu Lihat detail.
- Pagination memakai pilihan 10/25/50/100, default 10, dan state filter/sort dipertahankan pada query string. Sortir tanggal dan durasi berjalan server-side dengan indikator arah.
- Scope perusahaan dan izin summary tetap diperiksa server-side.

