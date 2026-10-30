# Perubahan Dashboard HRIS — Urutan dan Jarak Panel

## Perubahan

- Panel Early Warning approval ditempatkan secara visual di atas panel Karyawan terbaru.
- Kedua panel dibungkus dalam stack dengan jarak antar-panel serta padding vertikal agar tidak menempel pada panel statistik atau batas bawah halaman.
- Panel Early Warning hanya ditampilkan jika terdapat pengajuan yang benar-benar menunggu persetujuan.

## Verifikasi

- Memeriksa struktur Blade dashboard dan selector CSS yang digunakan untuk urutan panel.
- Tidak mengubah query, permission, atau data approval.

## Risiko tersisa

- Tampilan akhir tetap perlu dicek pada browser desktop dan mobile setelah asset CSS dibuild.
