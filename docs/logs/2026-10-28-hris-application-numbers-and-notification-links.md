# Nomor pengajuan HRIS dan tautan notifikasi approval

## Perubahan

- Menambahkan nomor pengajuan unik otomatis untuk cuti/sakit, lembur, reimbursement, dan cash advance.
- Menampilkan nomor pengajuan pada form, daftar, detail, dan notifikasi agar approver dapat mengenali pengajuan serta pemohonnya.
- Mengubah target notifikasi approval agar pengajuan yang sudah selesai tidak lagi diarahkan ke detail aksi yang menghasilkan 404; notifikasi tersebut diarahkan ke daftar approval.
- Menjaga struktur form pengajuan lama; field nomor pengajuan ditambahkan sebagai input readonly saja.

## Verifikasi

- Migration `2026_10_28_000014_add_request_numbers_to_hris_requests` berhasil dijalankan tanpa reset database.
- PHP lint untuk controller, service, model, dan Form Request berhasil; `git diff --check` tidak menemukan whitespace error.
- Tinker untuk verifikasi jumlah data tidak dapat dijalankan karena permission lokal pada `workspace/storage/logs/laravel.log`, bukan karena migration.
