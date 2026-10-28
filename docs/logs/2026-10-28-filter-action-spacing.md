# Penyelarasan jarak tombol filter

Tanggal: 2026-10-28

## Perubahan

- Menambahkan jarak internal 8px antara field filter terakhir dan grup tombol
  `Reset`/`Terapkan` melalui class global `.workspace-filter-actions`.
- Menetapkan ruang kiri melalui area isi grup tombol agar tetap terlihat pada
  layout grid khusus yang menggunakan kolom rapat.
- Menyamakan filter daftar Dokumen dengan pola Bootstrap grid pada halaman Tipe
  Dokumen, menggunakan `row g-2 align-items-end` dan kolom filter eksplisit.
- Menjaga `Reset` dan `Terapkan` tetap menjadi satu grup segmented control.
- Menetapkan aturan ini sebagai standar untuk seluruh halaman daftar yang sudah
  ada dan fitur baru.

## Verifikasi

- `git diff --check` tidak menemukan whitespace error.
