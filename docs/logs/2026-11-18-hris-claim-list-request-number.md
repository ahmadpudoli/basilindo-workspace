# Nomor pengajuan pada daftar klaim pribadi

Tanggal: 2026-11-18

## Perubahan

- Menambahkan kolom Nomor pada tabel pribadi Reimbursement & Cash Advance.
- Nomor ditampilkan menggunakan elemen `<code>` seperti nomor pada tabel Pengajuan Lembur.
- Baris Draft tanpa nomor menampilkan tanda `—`.
- Menyesuaikan lebar tujuh kolom dan colspan empty state.
- Memperbarui requirement dan checklist.

## Verifikasi

- `git diff --check`.

## Risiko tersisa

- Test otomatis tidak dijalankan untuk perubahan ini.
