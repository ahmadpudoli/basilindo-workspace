# Ringkasan Lembur HRIS

- Mengganti tiga kartu metrik menjadi satu panel berwarna yang compact.
- Menambahkan kolom nomor pengajuan, tabel responsif, sortir server-side pada tanggal/durasi, dan pagination dengan pilihan standar.
- Menambahkan pencarian nama/NIP/nomor pengajuan, filter multi-select perusahaan/status, serta rentang tanggal default awal bulan sampai hari ini.
- Menambahkan dropdown aksi tiga titik dengan menu Lihat detail.
- Kolom tanggal diselaraskan menjadi kolom Realisasi berisi jumlah rincian dan rentang tanggal berdasarkan detail realisasi; filter tanggal dan sortir tanggal juga memakai tanggal rincian.
- Periode default Ringkasan Lembur diperluas menjadi 1 Januari tahun berjalan sampai hari ini.
- Scope perusahaan tetap dibatasi server-side; filter perusahaan hanya dapat memilih perusahaan dalam scope pengguna.
- Verifikasi: sintaks PHP, kompilasi Blade, dan pemeriksaan diff; test suite tidak dijalankan.
