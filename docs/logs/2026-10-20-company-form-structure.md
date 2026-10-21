# Company Form Structure

## Perubahan

- Menjadikan jenis entitas dan klasifikasi sebagai dropdown wajib.
- Menambahkan pilihan parent perusahaan yang hanya memuat group aktif.
- Menambahkan validasi backend untuk entity type, classification, status, dan parent group.
- Menambahkan input multi-role perusahaan untuk menyimpan role bisnis seperti operating company, client, vendor, dan partner.
- Menetapkan role `group` secara otomatis berdasarkan jenis entitas `group`; role bisnis manual hanya ditampilkan untuk entitas `company` agar tidak ambigu dengan struktur perusahaan.
- Menyusun ulang field agar Jenis entitas berpasangan dengan Peran bisnis perusahaan, serta menerapkan floating label pada dropdown role.
- Menambahkan cakupan visibilitas many-to-many ke perusahaan operasional. Daftar perusahaan sekarang memakai irisan hak akses user dan visibilitas record; super admin tetap dapat melihat seluruh data.
- Memperbarui daftar perusahaan dengan badge jenis/status, font monospace untuk kode, serta pencarian dan pagination server-side melalui request AJAX tanpa reload halaman.

## Verifikasi

- PHP lint controller berhasil.
- `php artisan view:cache` berhasil.
- `npm.cmd run build` berhasil.
- `git diff --check` berhasil.
