# Ikon dan gaya dropdown aksi global

## Perubahan

- Menambahkan ikon pembayaran pada item Data pembayaran di dropdown aksi
  karyawan agar seluruh item (Data pembayaran, Ubah, Arsipkan) memakai ikon
  kontekstual.
- Menu disederhanakan menjadi daftar aksi saja: tanpa header, deskripsi, atau
  panel tambahan. Setiap aksi memiliki ikon kecil beraksen dan label satu baris.
- Menambahkan instruksi global untuk menu aksi compact dengan bayangan lembut,
  animasi ringan, serta state hover/focus yang jelas.
- Memperbarui CSS global dropdown dengan ukuran ringkas, radius lembut,
  transisi hover, dan perlakuan warna ikon yang semantik.

## Verifikasi

- Memeriksa komponen menu memakai `.workspace-table-action-menu` dan CSS global
  untuk item berikon, hover/focus, serta tampilan layar kecil.
- Test otomatis tidak dijalankan.
# Pembaruan ikon menu aksi karyawan

Menu Data pembayaran dilengkapi ikon kartu pembayaran bergaya SVG inline agar konsisten dengan ikon Ubah dan Hapus/Arsipkan pada dropdown aksi.

