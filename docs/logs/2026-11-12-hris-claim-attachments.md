# 2026-11-12 — Bukti lampiran reimbursement

## Perubahan

- Setiap rincian mewajibkan tanggal nota dan file bukti.
- Form menyediakan pemilih file dan aksi kamera mobile; validasi memeriksa tipe serta ukuran.
- Bukti diunggah serial per file ke prefix temporary private MinIO setelah validasi client berhasil. Tiap file maksimal 10 MB dan total pengajuan maksimal 50 MB.
- Token temporary terikat ke user. Server menyalin object ke prefix permanen dan menyimpan key pada rincian biaya; halaman persetujuan menyediakan unduh bukti dengan pemeriksaan pemohon, approver, permission, dan company scope.
- Temporary object berumur 30 menit dibersihkan setiap 10 menit oleh command terjadwal.
- Proses `schedule:work` dimasukkan ke `composer run dev` supaya cleanup berjalan pada environment lokal.
- Migration incremental menambahkan key object lampiran pada rincian biaya.
- Keputusan desain chunking dicatat di `requirements/hris-reimbursement-attachment-upload-decision.md`.

## Verifikasi

- `php -l` berhasil untuk controller upload, Form Request, service, controller HRIS, bootstrap, route, command, dan migration.
- `node --check resources/js/app.js` berhasil.
- `php artisan list --raw` menampilkan command cleanup; `route:list --path=workspace/hris/claims` menampilkan endpoint unggah, pembaruan draft, dan unduh bukti.
- `php artisan view:cache` berhasil dan cache kemudian dibersihkan; `git diff --check` tidak menemukan whitespace error.
- Vite build berhasil dengan `--configLoader runner --emptyOutDir false` dan memperbarui manifest/assets tanpa menghapus file hasil build lama.
- Migration incremental untuk object key bukti biaya berhasil diterapkan pada database development; penambahan kolom nullable dan backfill legacy menjaga data yang sudah ada.
- Test suite tidak dijalankan.

## Risiko tersisa

- Unggah dihentikan jika koneksi gagal; pengguna dapat mencoba lagi dan temporary object hasil percobaan yang tidak terpakai akan dibersihkan oleh TTL.
