# Preview dokumen pada modal — 2026-10-13

## Perubahan

- Tombol Preview pada detail dokumen membuka modal dialog Filament.
- Modal menampilkan PDF viewer melalui iframe ke endpoint preview private yang sudah tersedia.
- Endpoint tetap menjalankan policy `preview`, company scope, streaming dari MinIO, dan audit event.

## Verifikasi

- URL preview tidak diekspos sebagai public object URL.
- File dibaca melalui stream private dan dikirim dengan `Content-Disposition: inline`.
