# 2026-10-28 — Validasi tahun kuota pada revisi cuti

## Perubahan

- Memperbaiki resubmit agar array `dates` tidak lagi ditulis sebagai atribut model dan tidak menimpa relasi `dates()`.
- Membuat accessor tanggal aman terhadap koleksi/array relasi.
- Menambahkan resolusi kuota berdasarkan tahun kalender dan menolak pengajuan cuti jika tahun tersebut belum memiliki konfigurasi kuota aktif.
- Menambahkan test untuk tahun tanpa konfigurasi kuota dan memperbarui test kuota/revisi agar memakai konfigurasi tahun eksplisit.

## Verifikasi

- Test service mencakup kegagalan tahun tanpa konfigurasi, lintas tahun, kuota, dan resubmit.
- Kompilasi Blade serta build asset akan dijalankan setelah perubahan.

## Risiko tersisa

- Nilai default 12 hari tetap dapat ditampilkan pada UI lama, tetapi tidak lagi menjadi fallback saat menyimpan pengajuan cuti; penyimpanan tetap membutuhkan konfigurasi kuota tahun yang berlaku.
