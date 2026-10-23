# 2026-10-23 — Permission panel audit trail

## Perubahan

- Menambahkan permission modul `view_document_audit_trail` untuk panel audit subject Document Management.
- Menambahkan role `auditor` dan memberikan permission kepada `auditor`, `document_admin`, `finance_admin`, dan `finance_reviewer`; `super_admin` tetap memiliki seluruh permission.
- Policy dokumen dan tipe dokumen sekarang memeriksa permission audit setelah subject access.
- Controller tidak lagi memuat event audit ketika user tidak berhak, dan view menyembunyikan panel audit tanpa permission.
- Audit panel pada detail/edit tipe dokumen diverifikasi dengan permission yang sama; halaman audit global Core tetap dibatasi `super_admin` untuk mencegah paparan lintas perusahaan.
- Menambahkan migration incremental yang idempotent untuk permission dan role pada install yang sudah berjalan.

## Verifikasi

- Feature test memverifikasi panel dokumen dan tipe dokumen tersembunyi tanpa permission dan tampil dengan `view_document_audit_trail`.
- PHP lint, Blade view cache, dan frontend build dijalankan setelah perubahan.

## Risiko tersisa

- Hak akses audit tipe dokumen masih mengikuti policy visibility tipe dokumen yang sudah ada; permission audit tidak memperluas scope subject.
