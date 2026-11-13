# 2026-11-12 — Status menu aktif pada detail claim

## Perubahan

- Menambahkan route `workspace.hris.claims.show` ke pemetaan navigasi HRIS agar halaman detail claim memakai active key daftar reimbursement dan cash advance.
- Memperbarui requirement dan checklist.

## Verifikasi

- Memeriksa pemetaan menu sidebar dan menjalankan `php artisan view:cache` serta `git diff --check`.

## Risiko tersisa

- Tidak ada perubahan perilaku selain status aktif navigasi.
