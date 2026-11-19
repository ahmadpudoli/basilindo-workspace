# Popup pembayaran reimbursement, validasi, dan bukti

## Perubahan

- Mode ringkasan tidak lagi merender tabel daftar kerja kedua; route/handler halaman pembayaran terpisah dihapus, sehingga pencatatan dilakukan dari popup pada tabel ringkasan.
- Popup menambahkan unggah bukti opsional berupa JPEG, PNG, WebP, atau PDF. Gambar diperkecil maksimal 2000 px dan dikompresi ke JPEG pada browser bila hasilnya lebih kecil.
- Server memvalidasi MIME/ekstensi dan batas 10 MB, lalu menyimpan file ke disk private pada object key acak. Referensi object key disimpan pada kolom `payment_proof_object_key`; ID bukti manual tetap di kolom terpisah.
- Validasi client memeriksa isian wajib, opsi metode, data bank transfer, nominal, tanggal, panjang teks, tipe dan ukuran file. Server tetap menjadi validasi otoritatif dan mempertahankan pengecekan permission, scope, status, nominal, serta lock transaksi.
- Migration incremental menambahkan kolom object key. Rollback migration tidak menghapus kolom agar referensi file yang sudah dicatat tidak hilang.

## Verifikasi

- Meninjau secara statis flow render mode ringkasan, FormData multipart AJAX, kompresi browser, validasi server, disk privat, dan lock pembayaran.
- Test suite tidak dijalankan.

## Risiko/catatan

- File bukti tersimpan privat dan object key tercatat; endpoint preview/unduh untuk bukti pembayaran belum menjadi bagian perubahan ini.
- Kompresi browser memakai `createImageBitmap` dan Canvas; jika browser tidak mendukungnya, file gambar dikirim apa adanya dan tetap dibatasi ukuran server 10 MB.
