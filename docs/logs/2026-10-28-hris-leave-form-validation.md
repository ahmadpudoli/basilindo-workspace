# Validasi form pengajuan cuti dan sakit

Tanggal: 2026-10-28

## Perubahan

- Menonaktifkan pesan validasi HTML native pada form pengajuan cuti/sakit.
- Menambahkan validasi client-side untuk tanggal kosong, duplikat tanggal, batas sisa kuota, alasan wajib, dokumen sakit wajib, format dokumen, dan ukuran maksimal 10 MB.
- Menampilkan pesan kesalahan di dekat field terkait, memberi state `is-invalid`, dan memfokuskan field pertama yang bermasalah.
- Memperkuat validasi server-side dengan aturan `distinct` untuk tanggal dan pesan validasi yang konsisten.

## Verifikasi

- Feature test server-side validasi form berhasil: 1 test, 4 assertions.
- Test domain pengajuan cuti/sakit berhasil: 3 test, 4 assertions.

## Catatan

- Warning penulisan cache hasil Pest pada `vendor/pestphp/pest/.temp` berasal dari permission environment lokal dan tidak memengaruhi hasil test.
- Memperbaiki penyimpanan pengajuan cuti agar `start_date` dan `end_date` yang telah dihitung dari daftar tanggal ikut disimpan pada record utama.
