# Menu Pegawai pada sidebar HRIS

## Perubahan

- Menambahkan menu `Pegawai` ke sidebar HRIS.
- Menu mengarah ke `workspace.hris.employees` dan hanya tampil untuk pengguna dengan permission `view_any_employee`.

## Verifikasi

- Route `workspace.hris.employees` tersedia dan menggunakan controller `EmployeeController@index`.
- Filter navigasi sidebar tetap menerapkan permission pengguna dan scope modul HRIS.
- `git diff --check` dijalankan setelah perubahan.
