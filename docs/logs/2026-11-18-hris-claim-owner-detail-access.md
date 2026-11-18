# Pembatasan akses detail klaim pribadi

Tanggal: 2026-11-18

## Perubahan

- Route detail dari daftar Reimbursement & Cash Advance memeriksa pemilik/pemohon; draft sendiri tetap bisa dibuka pembuatnya.
- Route detail Ringkasan memiliki ability terpisah yang mensyaratkan permission ringkasan sesuai tipe atau `view_any_claim`, scope perusahaan, dan klaim non-draft.
- Dropdown detail memakai ability yang sesuai dengan halaman asal, tanpa mengubah route summary dan navigasi aktif.
- Memperbarui requirement dan checklist akses detail.

## Verifikasi

- `php -l` pada policy/controller.
- `git diff --check`.

## Risiko tersisa

- Test otomatis tidak dijalankan untuk perubahan ini.
