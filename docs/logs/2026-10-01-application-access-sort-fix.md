# 2026-10-01 — Perbaikan Application Access list

## Requirement dan acceptance criteria

- Halaman `/admin/application-accesses` harus dapat dimuat tanpa SQL error.
- Sorting awal harus memakai kolom nyata pada `application_user_access`.
- Model pivot dengan composite primary key tidak boleh membuat Filament menghasilkan nama kolom kosong.

## Diagnosis

`ApplicationUserAccess` tidak memiliki satu primary key karena tabel memakai primary key gabungan
(`application_id`, `user_id`, `role_code`). Tanpa `defaultSort`, Filament mencoba memakai key
model yang bernilai kosong dan membangun query `order by "application_user_access".""`.

## Perubahan

- Menambahkan `->defaultSort('created_at', 'desc')` pada `ApplicationAccessResource`.
- Menambahkan fallback `getKeyName()` ke `created_at` pada `ApplicationUserAccess`, karena Filament juga menambahkan key sort stabil di luar `defaultSort()`.
- Menambahkan unit test untuk memastikan qualified key tidak pernah kosong.
- Menambahkan catatan pada checklist identity dan akses.

## Verifikasi

- Source resource dan model diperiksa; sort default serta fallback key menunjuk ke kolom `created_at` yang tersedia pada migration.
- `php -l app/Models/ApplicationUserAccess.php` berhasil.
- `php artisan optimize:clear` berhasil.
- `php artisan test --filter=ApplicationAccess --compact` berjalan, tetapi belum menemukan test sebelum unit test ditambahkan; test penuh perlu dijalankan berikutnya.

## Risiko tersisa

- Edit/delete record pivot composite key tetap perlu diuji terpisah bila action tersebut ditambahkan di masa depan.
- Server yang berjalan dengan worker PHP/Octane lama perlu direstart jika masih menampilkan query lama.

## UI sidebar

- Menambahkan active navigation state dengan surface navy gelap, teks putih, inset marker biru, dan focus ring yang terlihat.
- Kontras aktif ditujukan agar memenuhi prinsip visual hierarchy dan aksesibilitas; status aktif tetap dibedakan melalui background, marker, dan tipografi.
