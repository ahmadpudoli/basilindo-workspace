# Batas lebar kolom kategori claim

## Perubahan

- Mengatur layout tabel klaim agar kolom kategori maksimum 19%/290 px.
- Badge kategori membungkus di dalam kolom; pada layar kecil tabel mempertahankan lebar minimum dan dapat digeser horizontal.
- Kolom Status dibatasi menjadi sekitar 17%/maksimum 275 px dan kolom Aksi diperluas menjadi 25%.
- Kolom Aksi memakai seperempat lebar tabel, lebar minimum 360 px, dan tidak membungkus tombol agar aksi draft tampil satu baris.
- Sel Aksi diberi padding vertikal agar tombol tidak menempel pada garis baris.
- Memindahkan Edit draft dan Hapus draft yang dirender JavaScript ke container flex yang sama dengan tombol Detail; aksi kini konsisten satu baris dan memiliki padding vertikal.
- Acceptance criteria dan checklist HRIS diperbarui.

## Verifikasi

- `npm.cmd run build -- --configLoader runner` berhasil; Vite menghasilkan CSS dan JS production.
- Test suite tidak dijalankan.
