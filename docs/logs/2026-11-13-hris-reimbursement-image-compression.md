# Kompresi gambar bukti reimbursement

## Perubahan

- Foto dari kamera dan gambar yang dipilih dari galeri sekarang diproses di browser sebelum diunggah.
- Gambar di-resize bertahap dari sisi terpanjang 2.400 px hingga minimum 1.600 px, kualitas JPEG diturunkan bertahap, dan target ukuran sekitar 1,5 MB; nama attachment disesuaikan ke `.jpg`.
- PDF tidak diubah. Jika browser tidak dapat memproses gambar atau output tidak lebih kecil dari file awal, sistem mempertahankan file asli.
- UI menampilkan ukuran sebelum/sesudah kompresi atau informasi fallback.
- Mode zoom 100% menghitung ukuran pas area viewer; zoom tambahan diterapkan relatif terhadap hasil fit dan tetap dapat digeser.
- Requirement dan checklist diperbarui.

## Verifikasi

- Meninjau validasi server; endpoint menerima PDF/JPG/JPEG/PNG/WEBP/HEIC/HEIF sehingga hasil JPEG kompatibel.
- Test otomatis tidak dijalankan.

## Risiko tersisa

- Format gambar yang tidak dapat didekode browser atau yang hasil kompresinya lebih besar tetap diunggah dalam ukuran asli dan tunduk pada batas server 10 MB per file.
