# HRIS Claim Revision Draft

Tanggal: 2026-11-12

## Perubahan

- Form revisi reimbursement kini menyediakan aksi `Simpan perubahan` dan `Kirim revisi`.
- Simpan perubahan memperbarui data dan rincian biaya sambil mempertahankan status `needs_revision`, catatan revisi, serta snapshot/approval history; perubahan dapat dikirim kemudian.
- Kirim revisi tetap melalui dialog konfirmasi sebelum mengembalikan pengajuan ke Atasan atau Finance sesuai tipe revisi.
- Acceptance criteria dan checklist reimbursement diperbarui.

## Verifikasi

- `php artisan view:cache`.
- `php -l app/Modules/HRIS/Application/Services/ClaimService.php`.
- `php -l app/Modules/HRIS/Application/Services/HrisOperationsController.php`.

## Risiko tersisa

- Migrasi tidak diperlukan. Test suite tidak dijalankan untuk perubahan ini.
