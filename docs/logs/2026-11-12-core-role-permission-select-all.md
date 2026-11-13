# 2026-11-12 — Sinkronisasi pilih semua permission role

## Perubahan

- Menyinkronkan penghitung permission grup dan subgrup saat pilih semua modul ditekan.
- Menambahkan checkbox pilih semua pada setiap subgrup fitur.
- Menjaga status checked dan indeterminate pada kontrol grup/subgrup saat permission individual berubah.
- Memperbarui requirement dan checklist.

## Verifikasi

- Meninjau inisialisasi counter dari permission yang sudah tersimpan serta sinkronisasi kontrol grup dan subgrup.
- Pemeriksaan sintaks JavaScript melalui build frontend dan `git diff --check`.

## Risiko tersisa

- Belum melakukan klik manual melalui browser.
