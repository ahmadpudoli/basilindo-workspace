# Penyatuan dashboard HRIS

Tanggal: 2026-11-19

## Perubahan

- Dashboard HRIS kini selalu merender `modules.hris-dashboard` sebagai satu sumber tampilan.
- Kartu pengajuan pribadi memakai slot, grid, dan komponen kartu metrik yang sama dengan dashboard acuan; CTA tetap mengikuti permission yang dimiliki.
- KPI pribadi dihitung dan ditampilkan untuk semua pengguna HRIS, termasuk pengguna yang juga memiliki akses daftar karyawan; query tetap dibatasi ke pengajuan milik akun login.
- KPI tenaga kerja dan tabel karyawan hanya ditampilkan kepada pengguna dengan permission terkait; panel approval mengikuti permission approval dan hanya muncul bila ada item menunggu.
- View dashboard pribadi lama tidak lagi digunakan oleh controller.
- Requirement dan checklist HRIS diperbarui.

## Verifikasi

- `php -l` berhasil pada controller dan `php artisan view:cache` berhasil mengompilasi template setelah KPI pribadi tidak lagi bergantung pada permission daftar karyawan.
- `git diff --check` hanya melaporkan peringatan normal konversi akhir baris pada file kerja yang sudah berubah; tidak ada whitespace error.
- Test aplikasi tidak dijalankan.

