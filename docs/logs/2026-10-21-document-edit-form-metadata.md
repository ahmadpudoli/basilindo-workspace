# 2026-10-21 — Form edit dan metadata tambahan dokumen

## Perubahan

- Menyamakan susunan form edit dengan form tambah.
- Menambahkan panel lampiran read-only yang ringkas dan panel editor deskripsi Summernote.
- Memindahkan field metadata dinamis ke panel tambahan di bawah informasi utama.
- Menambahkan validasi server agar jenis dokumen aktif dan sesuai dengan perusahaan yang dipilih, termasuk pada update.
- Memperbarui feature test untuk memeriksa panel metadata dan field definisi tipe dokumen.

## Verifikasi

- `npm.cmd run build`
- `php artisan view:cache`
- `php artisan test --filter=DocumentEditViewTest`
- `git diff --check`

## Risiko tersisa

- Preview file yang tersimpan tetap diarahkan ke halaman detail karena form edit tidak mengubah object storage.
