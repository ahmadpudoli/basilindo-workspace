# Perbaikan tabel ringkasan reimbursement

Tanggal: 2026-11-18

## Perubahan

- Menyamakan tampilan nomor pengajuan reimbursement dengan gaya nomor pengajuan pada Ringkasan Cuti Karyawan.
- Menata ulang proporsi kolom ringkasan dan membungkus teks panjang di dalam kolomnya supaya tidak menimpa isi kolom lain.
- Mengizinkan pengguna dengan permission `view_any_claim` membuka detail klaim berstatus `needs_revision` selama klaim berada dalam scope perusahaan. Draft tetap hanya terlihat oleh pemilik; hak edit tetap dibatasi policy pemilik.
- Memperbarui acceptance criteria dan checklist reimbursement.

## Verifikasi

- `php -l workspace/app/Modules/HRIS/Application/Services/ClaimPolicy.php`
- `git diff --check`

## Risiko tersisa

- Test otomatis tidak dijalankan untuk perubahan ini.
