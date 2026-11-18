# Permission cetak pengajuan HRIS

Tanggal: 2026-11-18

## Perubahan

- Menambahkan permission `print_hris_reimbursement`, `print_hris_cash_advance`, `print_hris_overtime`, dan `print_hris_leave` di grup Approval & ringkasan pada assignment role dan user.
- Menampilkan label bisnis berbahasa Indonesia dan memprovisikan permission secara incremental; role admin HRIS dan super admin menerima grant awal.
- Permission Cetak diletakkan sebagai tambahan pada grup Approval & ringkasan yang sudah ada; daftar lama tetap ditampilkan.
- Pemilik/pemohon otomatis dapat mencetak reimbursement tanpa permission khusus. Pengguna lain perlu permission reimbursement serta scope perusahaan.
- Menegakkan permission pada endpoint print, tombol halaman detail, daftar pribadi, ringkasan, dan pratinjau lampiran; hanya reimbursement yang sudah mendapat persetujuan Finance dan seluruh approval yang dapat dicetak saat ini.
- Permission untuk Cash Advance, Lembur, dan Cuti disediakan untuk alur cetaknya; print endpoint untuk tipe tersebut belum tersedia.
- Memperbarui requirement dan checklist.

## Verifikasi

- `php -l` pada policy, controller, seeder, dan migration.
- Migration permission incremental berhasil diterapkan ke database lokal.
- `php artisan view:cache` berhasil.
- `git diff --check`.

## Risiko tersisa

- Test otomatis tidak dijalankan untuk perubahan ini.
