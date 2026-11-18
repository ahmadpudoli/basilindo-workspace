# Penghapusan garis metadata referensi lembur

## Perubahan

- Menghapus garis horizontal di atas metadata Client, pemilik perusahaan internal, dan status referensi pada accordion detail lembur.
- Menghapus margin dan padding atas metadata yang masih menyisakan ruang kosong setelah garis dihilangkan.
- Memperbarui requirement dan checklist.

## Verifikasi

- Pemeriksaan diff dan `git diff --check`.
- Tidak menjalankan test suite.

## Risiko tersisa

- Tidak ada perubahan backend atau authorization.
