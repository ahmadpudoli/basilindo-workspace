# Penyegaran detail approval lembur

## Perubahan

- Menghapus bagian dokumen pendukung dari halaman approval lembur, termasuk modal preview khusus pada halaman ini.
- Menata pemohon, nomor pengajuan, perusahaan, status, waktu pengajuan, unit, jenis/lokasi, durasi, dan alasan menjadi ringkasan dua kolom dengan label dan nilai yang jelas.
- Memakai latar lembut dan separator tipis untuk pengelompokan; setiap field tetap berada pada satu panel bersama, bukan kartu terpisah.
- Menjaga tabel rincian lembur tepat di bawah informasi permohonan.
- Memperbarui requirement dan checklist.

## Verifikasi

- Pemeriksaan diff dan `git diff --check`.
- Test suite dan browser tidak dijalankan.

## Risiko tersisa

- Perlu pemeriksaan visual di halaman approval pada desktop dan mobile setelah aplikasi dimuat ulang.
