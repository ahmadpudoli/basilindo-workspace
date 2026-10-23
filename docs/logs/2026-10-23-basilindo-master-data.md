# Provisioning master data Basilindo

## Perubahan

- Menambahkan migration `2026_10_23_000003_provision_basilindo_master_data`.
- Menyediakan Group Basilindo dan enam perusahaan internal: LTN, LEGOSPAY, QUINSIS, M3, MAGIS, dan CARANO.
- Memprovision 57 permission dan 11 role baseline yang saat ini digunakan aplikasi.
- Mengganti akun legacy `admin@example.com` menjadi `admin@basilindo.co.id` bila ditemukan, atau membuat akun baru dengan `is_system_account=true` dan role `super_admin`.
- Menghubungkan akun sistem ke seluruh perusahaan melalui `company_user`.
- Mengunci permission role `super_admin` pada backend/form dan mengunci role, company scope, serta permission akun sistem pada form user.

## Verifikasi

- `php artisan migrate --force` — migration berhasil dijalankan.
- Akun terverifikasi: email baru, `is_system_account=true`, role `super_admin`, 57 permission, dan relasi perusahaan.
- `php artisan view:cache` berhasil.
- `git diff --check` tidak menemukan whitespace error baru.

## Catatan

- Logo dari gambar belum diisi sebagai binary/object storage karena file asset logo tidak tersedia sebagai file repository pada saat migration dibuat. Kolom brand asset tetap dipertahankan untuk provisioning asset melalui UI/storage privat.
- Migration tidak menghapus perusahaan, role, permission, atau data existing saat rollback.
