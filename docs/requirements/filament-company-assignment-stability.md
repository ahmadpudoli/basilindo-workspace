# Requirement — Assignment perusahaan stabil di Filament

## Tujuan

Assignment perusahaan pada relation manager user harus memakai lifecycle action Filament tanpa memaksa morph/reopen tabel melalui event browser.

## Acceptance criteria

- Submit `Tambah perusahaan` menyimpan company dan `scope_role`, menutup slide-over, lalu menampilkan data terbaru.
- Aksi detach dan detach bulk tetap me-refresh tabel melalui lifecycle Livewire.
- Tidak ada event browser atau overlay custom yang memasang ulang action ketika response Livewire sedang dimorph.
- Halaman relation manager tidak menghasilkan `ReferenceError` untuk `columns`, `getSelectedRecordsCount`, atau `canSelectAllRecords`.

## Requirement UX

- Form user menampilkan ikon informasi dengan tooltip pada Role aplikasi.
- Form assignment perusahaan menampilkan tooltip pada perusahaan dan Peran user di perusahaan.
- Kolom peran pada daftar perusahaan menjelaskan perbedaannya melalui tooltip, tanpa menambah blok teks permanen pada halaman.

# Requirement — Single super admin

## Acceptance criteria

- Hanya akun sistem `admin@example.com` yang dapat memiliki role `super_admin`.
- Role `super_admin` tidak dapat dipilih untuk user baru atau user lain melalui UI.
- Role aplikasi pada akun sistem tidak dapat diubah dari halaman edit user.
- Akun sistem dan role `super_admin`-nya tidak dapat dihapus melalui UI, model, bulk query, atau database trigger.
- Percobaan memberikan role `super_admin` kepada user lain ditolak oleh PostgreSQL.
- Menyimpan profil akun sistem tidak boleh menjalankan sinkronisasi pivot role atau menghapus `super_admin`.
