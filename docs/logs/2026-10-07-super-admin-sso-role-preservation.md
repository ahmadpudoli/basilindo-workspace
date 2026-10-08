# Log pekerjaan — Preservasi super admin saat SSO

## Perubahan

- Menemukan bahwa `syncMappedRoles()` dapat memanggil `syncRoles()` pada akun sistem dan menghapus `super_admin` ketika claim SSO hanya berisi role `admin`.
- Memastikan role `super_admin` selalu dipertahankan pada akun sistem `admin@example.com` sebelum role SSO disinkronkan.
- Menghentikan seluruh `syncRoles()` untuk akun sistem; callback SSO hanya memastikan `super_admin` tersedia dan tidak memproses claim role provider untuk akun tersebut.

## Verifikasi

- Syntax check controller dilakukan.
- Jalur role SSO tidak lagi mengirim daftar role yang menghilangkan `super_admin` dari akun sistem.
