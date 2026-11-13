# 2026-11-12 — Revisi workflow claim

## Perubahan

- Mengaktifkan permintaan revisi claim pada tahap Atasan dan Finance, dengan pilihan revisi substansi/administrasi serta catatan wajib.
- Revisi substansi mengembalikan pengajuan ke Atasan; revisi administrasi mengembalikan ke tahap yang meminta revisi.
- Pemohon dapat membuka form dari status Perlu revisi, memperbarui header/rincian/bukti, dan mengirim ulang nomor claim yang sama.
- Mengirim ulang memperbarui rincian aktif, menandai rincian yang dikeluarkan sebagai tidak aktif, menyimpan snapshot data/rincian terdahulu, dan mempertahankan object bukti lama.
- Halaman detail claim menampilkan riwayat approval, catatan revisi, pengiriman ulang, dan ringkasan snapshot yang tersimpan.
- Panel riwayat dilindungi permission granular `view_claim_audit_trail` dan policy subject scope; permission ditetapkan untuk `hris_admin` dan `super_admin` serta dapat diberikan lewat editor role.
- Menambahkan metadata requester/revisi dan tabel snapshot lewat migration incremental. Rollback sengaja mempertahankan kolom dan snapshot historis.
- Menambahkan test alur routing untuk empat kombinasi tahap dan jenis revisi, snapshot, serta policy pemohon.

## Verifikasi

- PHP lint lulus untuk service, controller, model, migration, dan test yang berubah.
- `php artisan test --filter='claim revision' --do-not-cache-result`: 4 test lulus, 20 assertion termasuk akses audit authorized/unauthorized.
- `php artisan view:cache` berhasil.
- `git diff --check` tidak menemukan whitespace error.
- Pest memberi peringatan karena cache hasil pada `vendor/pestphp/pest/.temp/test-results` tidak dapat ditulis; eksekusi tetap berjalan.

## Risiko tersisa

- Preview file lama yang sudah diganti tetap terlindungi dan object-nya dipertahankan; UI saat ini menampilkan bukti versi aktif, sedangkan detail versi sebelumnya tersimpan di snapshot.
