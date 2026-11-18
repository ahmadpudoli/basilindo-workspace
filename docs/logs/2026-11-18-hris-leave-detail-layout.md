# Perapihan layout detail cuti/sakit

Tanggal: 2026-11-18

## Perubahan

- Memindahkan panel dokumen pendukung ke dalam panel tanggal/alasan tepat di bawah alasan pengajuan.
- Mempertahankan kontrol preview dan unduh beserta route authorization yang ada.
- Mengubah timeline approval menjadi panel collapsible yang expanded saat detail dibuka.
- Merapikan header, ringkasan, tanggal, alasan, dokumen, serta timeline dengan warna lembut, ikon SVG, dan spacing compact yang responsif.
- Menjaga kotak tanggal dengan latar biru lembut, sementara alasan tampil tanpa kartu dan dokumen menggunakan baris sederhana dengan divider.

## Verifikasi

- Blade cache dan `git diff --check` dijalankan setelah perubahan.

## Risiko tersisa

- Perubahan tampilan tidak mengubah permission atau sumber data timeline.
