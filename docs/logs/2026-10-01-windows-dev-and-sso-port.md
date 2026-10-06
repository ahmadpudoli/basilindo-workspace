# 2026-10-01 — Windows development command dan port SSO

## Perubahan

- `project-sso/composer.json` tidak lagi menjalankan `php artisan pail` sebagai bagian dari `composer run dev` karena Pail membutuhkan ekstensi PHP `pcntl` yang tidak tersedia pada Windows.
- Pail tetap tersedia melalui command opsional `composer run dev:logs` pada environment yang menyediakan `pcntl`.
- Server Laravel project SSO dipastikan berjalan pada `127.0.0.1:8001`.
- `APP_URL` dan `SSO_ISSUER_URL` project SSO serta `SSO_ISSUER_URL` File Organizer diselaraskan ke port `8001`.
- Test discovery OIDC diselaraskan dengan issuer port `8001`.

## Catatan

- Port `5173`/`5174` adalah port Vite untuk asset development, bukan port aplikasi Laravel atau issuer SSO. Vite menggunakan `5174` bila `5173` sedang dipakai proses lain.
- `composer run dev` menjalankan server Laravel, queue listener, dan Vite. Log Pail dipisahkan agar proses development Windows tidak gagal hanya karena `pcntl`.

## Verifikasi

- `composer validate --no-check-publish` — passed.
- `php artisan optimize:clear` pada project SSO dan File Organizer — passed.
- `php artisan test` project-sso — passed, 16 tests / 35 assertions.
- `php artisan test` file-organizer — passed, 27 tests / 56 assertions.
- `npm.cmd run build` project-sso — passed.
- Build File Organizer berhasil melalui output verifikasi sementara; output standar sempat gagal karena file `public/build` dikunci proses Vite lama yang sedang berjalan.
