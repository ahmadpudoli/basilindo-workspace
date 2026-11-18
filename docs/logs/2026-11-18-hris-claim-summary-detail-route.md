# Route detail klaim dari ringkasan reimbursement

Tanggal: 2026-11-18

## Perubahan

- Menambahkan route detail summary yang memakai controller dan Blade detail klaim yang sama dengan detail daftar pribadi.
- Link detail ringkasan membawa filter sebelumnya; tombol Kembali menuju ringkasan reimbursement dengan filter tersebut.
- Sidebar menjaga submenu Ringkasan Reimbursement aktif pada route detail summary.
- Policy detail mempertahankan akses owner, akses `view_any_claim` dalam scope perusahaan, dan akses permission ringkasan sesuai jenis klaim dalam scope perusahaan. Draft tetap dibatasi kepada pemilik/pembuat atau super admin.
- Memperbarui requirement dan checklist.

## Verifikasi

- `php -l` pada controller dan policy.
- `git diff --check`.

## Risiko tersisa

- Test otomatis tidak dijalankan untuk perubahan ini.
