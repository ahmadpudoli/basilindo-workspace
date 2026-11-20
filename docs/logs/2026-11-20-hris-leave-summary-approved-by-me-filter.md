# Filter approval pribadi pada ringkasan cuti dan sakit — 2026-11-20

## Perubahan

- Menambahkan filter “Disetujui oleh saya” pada Ringkasan Cuti Karyawan.
- Filter mencocokkan event `approve` untuk request type `leave` oleh pengguna login pada tahap atasan maupun HR.
- Dropdown yang sama kini juga memfilter event `reject` melalui pilihan “Ditolak oleh saya”.
- Filter memengaruhi daftar, metrik ringkasan, dan jumlah hari disetujui serta dapat digabungkan dengan filter jenis, status, tahun, kata kunci, dan pagination.
- Menjaga scope perusahaan dan permission halaman yang sudah ada; memperbarui requirement serta checklist.

## Verifikasi

- Belum menjalankan test atau build.

## Risiko tersisa

- Hasil filter belum diperiksa langsung di browser atau database aplikasi.
