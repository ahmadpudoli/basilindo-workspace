# Perbaikan temporary upload dokumen — 2026-10-13

## Perubahan

- Temporary upload Livewire diarahkan ke folder `workspace-tmp` pada disk MinIO/S3 private melalui `LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK` dan `LIVEWIRE_TEMPORARY_FILE_UPLOAD_DIRECTORY`.
- Checksum tidak lagi dihitung dengan `hash_file()` secara langsung.
- Upload membaca source melalui stream, sehingga tidak bergantung pada path lokal dari temporary file.
- Temporary object dihapus setelah object permanen dan metadata database berhasil disimpan.
- Cleanup terjadwal menghapus object pada `workspace-tmp` yang lebih tua dari TTL (`WORKSPACE_TEMPORARY_UPLOAD_TTL_HOURS`, default 24 jam).
- Lifecycle rule bawaan Livewire tidak dipakai karena MinIO lokal menolak request konfigurasi lifecycle dari AWS SDK akibat `Content-MD5`.
- Jika temporary file hilang atau sudah kedaluwarsa, pengguna mendapat pesan untuk memilih file kembali.

## Verifikasi

- Test upload dokumen tetap lulus.
- File permanen tetap ditulis ke disk S3/MinIO dengan object key private.
