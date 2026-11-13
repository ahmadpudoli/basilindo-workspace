# Kamera dan pratinjau bukti reimbursement

Tanggal: 2026-11-12

## Perubahan

- Tombol Ambil foto membuka kamera browser melalui `getUserMedia`, memakai kamera belakang jika tersedia, memperlihatkan pratinjau, dan menghasilkan JPEG yang masuk pada alur unggah temporary MinIO yang sama.
- Jika kamera tidak tersedia atau izin ditolak, form menampilkan alasan dan menyediakan tombol pilih file.
- Lampiran gambar menampilkan thumbnail, jenis file lain menampilkan nama dan ikon dokumen, dan lampiran dapat dihapus/diganti.
- Validasi server menolak rincian tanpa bukti, termasuk jika bukti draft dihapus tanpa file pengganti. Draft yang memiliki lampiran lama tetap dapat disimpan.
- URL pratinjau lampiran draft melewati endpoint unduh terotorisasi yang sudah ada.
- Memperbaiki pemaparan Bootstrap dari bundle module ke `window` agar pemanggilan modal inline berjalan; pada browser non-secure atau tanpa `getUserMedia`, tombol langsung memanggil pemilih kamera/file native dengan atribut capture.

## Verifikasi

- Modal kamera hanya menyediakan Batal dan Ambil foto; pilihan file tetap tersedia pada form.
- Belum menjalankan test suite sesuai instruksi sesi.
- Perlu uji manual kamera di browser laptop dan perangkat mobile; akses kamera mensyaratkan secure context (localhost atau HTTPS) dan izin kamera browser.
