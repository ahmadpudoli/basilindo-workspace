# Otorisasi owner detail lembur

## Perubahan

- Menambahkan policy `viewOwner` untuk pemohon, pembuat, atau user pemilik Employee.
- Route detail dari Pengajuan Lembur kini memeriksa owner scope seperti detail Cuti & Sakit.
- Route khusus detail Ringkasan Lembur tetap memeriksa permission `view_overtime` dan scope perusahaan melalui policy `view`.
- Memperbarui requirement dan checklist.

## Verifikasi

- Pemeriksaan policy/controller dan `git diff --check`.
- Tidak menjalankan test suite.

## Risiko tersisa

- Test authorization lintas owner dan permission belum dijalankan.
