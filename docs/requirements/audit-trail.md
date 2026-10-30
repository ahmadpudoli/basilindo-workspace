# Requirement: Audit trail Workspace

## Tujuan

Workspace harus menyimpan jejak aktivitas pengguna dan perubahan data agar admin dapat melakukan penelusuran operasional dan investigasi dengan aman.

## Acceptance criteria

- Event menyimpan actor, waktu, modul, aksi, subject, request ID, IP address, user agent, device/browser/OS, HTTP method, dan route.
- Perubahan data menyimpan snapshot `old_values` dan `new_values` dalam JSON; password, token, file binary, dan secret tidak boleh masuk snapshot.
- Seluruh operasi Create, Update, dan Delete pada resource bisnis aktif wajib menghasilkan audit event, termasuk perubahan melalui relasi atau service; event tidak boleh menggandakan pencatatan eksplisit yang sudah tersedia.
- View/preview/download dokumen yang dikategorikan penting atau rahasia wajib menghasilkan event akses tanpa menyimpan isi file, token, atau object binary.
- Login berhasil, login gagal, logout, upload, update, archive, preview, permission, dan aksi domain yang sudah tersedia dicatat melalui service audit terpusat.
- Halaman audit berada di Core, hanya dapat dibuka oleh role `super_admin`, memiliki filter server-side untuk kata kunci, modul, pengguna, dan rentang tanggal, serta pagination.
- Edit dokumen menampilkan history dokumen pada side panel yang dapat diciutkan dan memuat waktu, actor, device, serta snapshot perubahan.
- Panel audit pada subject menggunakan pola sidebar horizontal reusable: default collapsed, tombol tab dengan ikon untuk expand/collapse, dan transisi ke samping; jangan menggunakan collapse vertikal ke atas/bawah.
- Sidebar audit subject memiliki tinggi maksimum proporsional terhadap viewport (maksimal 720px pada desktop dan 620px pada layar lebih kecil); isi history menggunakan vertical scroll internal ketika melebihi tinggi tersebut, sementara header panel tetap terlihat.
- Panel audit subject dokumen dan tipe dokumen hanya ditampilkan kepada user dengan permission modul `view_document_audit_trail` dan subject access yang valid; `super_admin` selalu memiliki akses.
- Permission `view_document_audit_trail` dapat diberikan kepada role `document_admin`, `finance_admin`, `finance_reviewer`, atau role khusus `auditor`; role `member` tidak mendapat akses secara default.
- Seluruh panel subject audit yang tersedia saat ini—dokumen edit/detail dan tipe dokumen edit/detail—wajib memakai permission dan policy yang sama. Halaman audit global Core tetap khusus `super_admin` karena memuat lintas subject dan lintas perusahaan.
- Standar untuk modul berikutnya: panel audit trail baru wajib memakai permission granular milik modul (misalnya `view_crm_audit_trail` untuk CRM), policy scope subject, query audit yang hanya dijalankan setelah authorization, Blade gating, dan test untuk user tanpa/dengan permission. Jangan memakai permission Core generik atau mengandalkan akses `view` subject sebagai pengganti permission audit.
- Detail subject dokumen memiliki link kembali ke daftar, tombol edit terotorisasi, preview file, metadata dokumen, serta panel audit horizontal.
- Tipe Dokumen memiliki detail terpisah dan form edit dengan panel audit subject yang sama, serta redirect create/update ke halaman detail.
- Audit event append-oriented dan tidak mengubah data bisnis; migration harus incremental dan mempertahankan event lama.

## Keputusan implementasi

Audit memakai tabel Core `core_audit_logs` sebagai source of truth runtime. Migration incremental membuat tabel tersebut dan menyalin event lama dari `doc_audit_events`. Setelah verifikasi, tabel lama dipindahkan ke `legacy.doc_audit_events` dan tidak lagi digunakan runtime; histori tetap dipertahankan tanpa penghapusan permanen. Model, logger, halaman audit, dan panel subject menggunakan model Core `Core\\Models\\AuditLog`.
