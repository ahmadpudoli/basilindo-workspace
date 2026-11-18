# Penyegaran detail approval HRIS — 2026-11-17

## Perubahan

- Mempertahankan susunan panel detail pengajuan dan tindakan approval seperti sebelumnya.
- Membuat header pengajuan cuti/sakit lebih kuat dengan ikon kontekstual, nomor pengajuan, dan penanda tahap.
- Menambahkan ringkasan pemohon, perusahaan, waktu pengajuan, status/tahap, daftar tanggal, jumlah hari, dan alasan dengan layout responsif serta warna semantik.
- Mengubah timeline pengajuan cuti/sakit dan lembur menjadi accordion ringkas dengan ringkasan jumlah aktivitas, tahap saat ini, indikator warna, dan fokus keyboard yang jelas.
- Penyempurnaan berdasarkan review visual: header pengajuan memakai lebar panel penuh agar nama dan nomor pengajuan tidak terpotong; empat informasi utama digabung dalam strip ringkas dengan ikon dan pemisah halus, sementara tanggal dibuat lebih minimal dan alasan tetap menjadi callout.

## Verifikasi

- `php artisan view:cache` berhasil mengompilasi template Blade; `git diff --check` tidak melaporkan whitespace error.
- Test suite tidak dijalankan.

## Risiko/asumsi

- Timeline accordion dimulai tertutup agar detail pengajuan dan tindakan utama tetap ringkas; seluruh isi dapat dibuka dengan tombol keyboard/mouse.
