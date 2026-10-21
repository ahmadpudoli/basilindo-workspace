# Vendor CRUD parity

Tanggal: 2026-10-21

## Perubahan

- Melengkapi Vendor dengan halaman daftar, detail, create, edit, dan soft archive.
- Menerapkan company scope pada query daftar, detail, create, update, dan pilihan perusahaan.
- Menambahkan filter vendor berdasarkan perusahaan operasional dan status.
- Menambahkan search nama/kode, pagination, limit record, badge status, kode monospace, dan tabel compact.
- Menambahkan konfirmasi pada aksi arsip dan redirect detail setelah create/update.
- Menyelaraskan form, detail, empty state, action, dan pagination dengan Company UI.
- Menormalisasi relasi vendor: satu master Company dengan peran `vendor` dapat digunakan oleh banyak perusahaan operasional melalui `vendor_operational_companies`.
- Menambahkan kompatibilitas data lama melalui fallback `vendors.company_id` dan backfill relasi lama ke pivot.
- Mengubah form vendor agar memilih master perusahaan vendor dan multiple perusahaan operasional.

## Verifikasi

- PHP lint VendorController berhasil.
- Blade cache berhasil.
- Route Vendor berhasil terdaftar.
- Frontend build berhasil.
- `git diff --check` berhasil.

## Keputusan domain

Jika Carano dan LTN menggunakan vendor yang sama, PT ABC dicatat satu kali sebagai Company berperan `vendor`. Satu profil Vendor kemudian memilih PT ABC dan memilih Carano serta LTN sebagai perusahaan operasional pengguna. Profil vendor tidak dibuat ulang per perusahaan.
