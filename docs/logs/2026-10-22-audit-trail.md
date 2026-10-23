# Implementasi audit trail Workspace

## Perubahan

- Menetapkan `core_audit_logs` sebagai tabel audit Core dan membuat migration incremental untuk menyalin histori `doc_audit_events` tanpa menghapus sumber lama.
- Mengalihkan AuditLogger, halaman audit Core, serta panel audit dokumen dan tipe dokumen ke `Core\\Models\\AuditLog`.
- Memperluas `AuditLogger` sebagai service terpusat dengan metadata request dan snapshot JSON.
- Mencatat login berhasil/gagal, logout, perubahan user, upload/update/archive dokumen, serta event domain yang sudah memakai logger.
- Menambahkan halaman Audit trail pada Core dengan akses khusus `super_admin`, filter server-side, dan pagination.
- Menambahkan side panel history collapsible pada form edit dokumen.
- Merapikan filter audit dengan pilihan limit per halaman dan mengganti JSON inline menjadi modal detail.
- Mengubah history audit pada edit dokumen menjadi sidebar horizontal reusable yang default collapsed dengan tombol tab expand/collapse.
- Menambahkan halaman detail metadata dokumen dengan audit sidebar, link kembali ke daftar, tombol edit, dan redirect upload/update ke halaman detail.
- Menambahkan audit event, halaman detail, dan sidebar history horizontal pada Tipe Dokumen serta redirect create/update ke detail.
- Memindahkan source of truth runtime audit ke `core_audit_logs` melalui migration incremental; histori `doc_audit_events` dibackfill tanpa menghapus tabel lama.
- Mengalihkan AuditLogger, AuditController, dan panel audit subject ke `Core\\Models\\AuditLog`; `AuditEvent` lama dipertahankan sebagai compatibility alias.

## Verifikasi

- Migration audit berhasil dijalankan pada koneksi `documents`.
- `php artisan view:cache`
- `npm.cmd run build`
- PHP lint dan test target (`DocumentDomainTest`, `UserCanAccessPanelTest`) berhasil dijalankan.
- Migration `2026_10_22_000003_create_core_audit_logs` berhasil dijalankan pada database workspace.
- Verifikasi menunjukkan 15 event lama sudah tersalin ke `core_audit_logs`; `doc_audit_events` tidak lagi direferensikan runtime dan diarsipkan ke `legacy.doc_audit_events` melalui migration incremental.

## Risiko tersisa

- Modul lama yang belum memanggil `AuditLogger` secara eksplisit perlu diadopsi bertahap agar semua aksi domain memiliki event bermakna.

### Penyempurnaan tombol aksi tabel

- Menambahkan jarak horizontal global 6px pada seluruh elemen langsung di dalam `.workspace-table-actions`.
- Tombol aksi tetap menggunakan ukuran `sm` dan tetap rapat secara proporsional tanpa saling menempel.
- Verifikasi: `php artisan view:cache`, `npm.cmd run build`, dan `git diff --check` berhasil.

