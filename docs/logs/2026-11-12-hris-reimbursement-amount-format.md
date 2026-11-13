# Format nominal reimbursement

- Input nominal rincian memakai input teks numeric dengan locale Indonesia, dua digit desimal, dan pemisah ribuan.
- FormRequest menormalkan nilai seperti `1.234,56` menjadi angka desimal sebelum validasi; nominal dibatasi maksimal dua digit desimal.
- Referensi nota menampilkan placeholder contoh `INV-2026-00123` dan label `(Opsional)`.
- Verifikasi: PHP lint dan pemeriksaan diff; suite test/browser belum dijalankan.
