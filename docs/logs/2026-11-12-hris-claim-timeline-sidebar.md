# HRIS Claim Timeline Sidebar

Tanggal: 2026-11-12

## Perubahan

- Detail claim menggunakan layout dua kolom: rincian pengajuan di kiri dan sidebar timeline di kanan.
- Timeline mengurutkan aktivitas pengiriman, approval, permintaan revisi, pengiriman ulang, serta snapshot revisi secara kronologis.
- Timeline tetap dilindungi permission `view_claim_audit_trail` dan policy scope subject.
- Pada layar sempit sidebar turun ke bawah konten utama; pada layar lebar sidebar mengikuti scroll.
- Requirement dan checklist reimbursement diperbarui.

## Verifikasi

- `php artisan view:cache`.
- `php -l app/Modules/HRIS/Application/Services/HrisOperationsController.php`.
- `php -l app/Modules/HRIS/Domain/Models/Claim.php`.

## Risiko tersisa

- Tidak ada perubahan schema. Test suite tidak dijalankan untuk perubahan tampilan ini.
