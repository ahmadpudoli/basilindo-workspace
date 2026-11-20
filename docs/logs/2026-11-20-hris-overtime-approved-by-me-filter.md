# Filter approval pribadi pada ringkasan lembur — 2026-11-20

## Perubahan

- Menambahkan filter “Disetujui oleh saya” yang mencocokkan event approval lembur (`action=approve`) dari pengguna login pada tahap atasan atau HR.
- Memperluas filter riwayat approval dengan opsi “Ditolak oleh saya” yang mencocokkan event `action=reject` dari pengguna login.
- Filter diterapkan pada query utama sebelum pagination dan agregasi KPI, sehingga dapat dikombinasikan dengan filter perusahaan, status, periode, dan kata kunci.
- Menjaga scope perusahaan dan permission halaman yang sudah ada.
- Memperbarui requirement dan checklist.

## Verifikasi

- Belum menjalankan test atau build.

## Risiko tersisa

- Hasil filter belum diperiksa langsung di browser atau database aplikasi.
