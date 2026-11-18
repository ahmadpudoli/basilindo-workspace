# Route detail dari Ringkasan Lembur

## Perubahan

- Menambahkan route detail khusus di bawah path Ringkasan Lembur, memakai action controller dan Blade detail lembur yang sama.
- Mempertahankan pemeriksaan `OvertimePolicy::view` sehingga akses tetap dibatasi oleh kepemilikan atau permission dan scope perusahaan.
- Mengarahkan aksi Lihat detail dari tabel ringkasan ke route baru, dengan URL ringkasan saat ini sebagai tujuan kembali.
- Memvalidasi tujuan kembali agar hanya menerima path Ringkasan Lembur pada host aplikasi; fallback menuju ringkasan tanpa filter.
- Tombol Kembali pada resource detail bersama menggunakan tujuan yang diberikan controller; route lama tetap kembali ke daftar Pengajuan Lembur.
- Memperbarui requirement dan checklist.

## Verifikasi

- Pemeriksaan diff dan `git diff --check`.
- Tidak menjalankan test suite atau pemeriksaan browser.

## Risiko tersisa

- Perlu pemeriksaan manual bahwa menu tetap aktif dan filter pulih setelah membuka detail dari menu aksi.
