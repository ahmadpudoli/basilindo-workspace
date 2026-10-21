# Requirement: Lokasi runtime Workspace

## Tujuan

Mencatat lokasi aplikasi Laravel runtime Basilindo Workspace secara konsisten setelah dipindahkan dari app/workspace ke workspace.

## Acceptance criteria

- `workspace/` menjadi satu-satunya lokasi source dan runtime Laravel Workspace.
- Dokumentasi aktif, perintah setup, Docker, dan CI menggunakan `workspace/`.
- Referensi `app/workspace` yang tersisa hanya berada pada log historis atau konteks migrasi masa lalu.
- Perintah Composer, Artisan, dan npm dijalankan dari `workspace/`.
