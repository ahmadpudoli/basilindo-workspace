# Perbaikan grant default permission cetak HRIS

Tanggal: 2026-11-18

## Perubahan

- Menghapus grant default permission cetak dari role `admin` dan `hris_admin`; akses dapat diberikan secara eksplisit lewat form role.
- Memperbarui `RoleSeeder` agar seeding role `admin` tidak menambahkan kembali permission cetak.
- Menghapus bypass cetak berbasis role `super_admin`; selain pemilik/pemohon, seluruh pengguna harus memiliki permission cetak yang sesuai serta scope perusahaan.
- Menjaga hak pemilik/pemohon untuk mencetak pengajuan miliknya sesuai policy owner.

## Verifikasi

- Migration incremental menghapus hanya relasi grant empat permission cetak pada role `admin` dan `hris_admin`; baris master permission dan grant role lain tetap ada.
- Verifikasi migration, Blade cache, dan diff dijalankan setelah perubahan.

## Risiko tersisa

- Hak akses eksplisit lain pada role admin dan hris_admin tetap mengikuti konfigurasi role masing-masing.
