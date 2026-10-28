# 2026-10-28 — Enforcement cut-off pengajuan cuti

## Perubahan

- Menambahkan resolusi `hris.leave_submission_cutoff` berdasarkan perusahaan dan tahun kalender.
- Pengajuan cuti ditolak jika cut-off belum dikonfigurasi, formatnya tidak valid, atau tanggal cut-off sudah lewat.
- Cut-off diproses sampai akhir hari pada timezone `APP_TIMEZONE`/Asia Jakarta.
- Pengajuan sakit tidak terpengaruh oleh cut-off pengajuan cuti.
- Validasi nilai parameter cut-off menerima format `YYYY-MM-DD`.
- Test untuk konfigurasi kuota dan cut-off tahunan ditambahkan.

## Verifikasi

- Jalur create dan resubmit sama-sama menggunakan validasi service yang sama.
- Syntax PHP dan kompilasi Blade perlu diverifikasi pada stack aplikasi lokal.

## Risiko tersisa

- Administrator perlu mengisi parameter cut-off untuk setiap tahun cuti sebelum pengajuan cuti dibuka.
