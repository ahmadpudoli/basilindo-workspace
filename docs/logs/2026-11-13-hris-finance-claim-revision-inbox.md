# Aksi revisi claim pada approval Finance

## Perubahan

- Tabel inbox Persetujuan HRIS sekarang menampilkan tombol dan modal Minta revisi untuk claim pada tahap Finance, juga tahap Atasan.
- Modal menggunakan pilihan revisi substansi/administrasi dan catatan wajib yang sudah ditangani backend workflow.
- Requirement dan checklist HRIS diperbarui.

## Verifikasi

- Memeriksa route approval dan `HrisWorkflowService`: aksi revisi Finance divalidasi terhadap status `pending_finance`, konfigurasi approver aktif, jenis revisi, dan catatan.
- Test otomatis tidak dijalankan.

## Risiko tersisa

- Tidak ada perubahan status workflow atau aturan resubmit; fitur mengikuti jalur revisi claim yang sudah tersedia.
