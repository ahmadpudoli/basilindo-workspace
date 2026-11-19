# Keputusan penyimpanan bukti pembayaran reimbursement

- **Status:** Diterapkan — 2026-11-19.
- **Keputusan:** File bukti pembayaran disimpan di disk private HRIS memakai object key acak; kolom `payment_proof_id` tetap menjadi ID/referensi manual, dan kolom incremental `payment_proof_object_key` menyimpan lokasi file.
- **Validasi:** Bukti file wajib dilampirkan. Browser membatasi file gambar dan PDF maksimal 5 MB, mengompresi gambar sebelum upload, dan server menegakkan kewajiban serta batas 5 MB untuk semua format.
- **Alternatif:** Menggunakan kolom ID yang sama untuk object key ditolak karena mencampur identifier bisnis dengan lokasi storage. Menyimpan file publik ditolak karena bukti pembayaran bersifat privat.
- **Catatan:** Akses preview/unduh bukti lewat endpoint terotorisasi akan ditambahkan saat UI peninjauan bukti pembayaran dibangun.
