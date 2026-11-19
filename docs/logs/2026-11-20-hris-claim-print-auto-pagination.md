# Pagination otomatis dokumen cetak reimbursement

- Menghapus page break paksa sebelum rincian biaya.
- Pratinjau kini mengukur ruang kosong pada lembar aktif dan hanya memindahkan panel rincian biaya atau tanda tangan ke lembar berikutnya saat panel tersebut tidak muat.
- Lampiran tetap selalu dimulai pada lembar baru; ukuran lembar mengikuti pilihan kertas.
- Verifikasi: `node --check resources/js/claim-print.js`, kompilasi Blade, dan pemeriksaan diff whitespace.
