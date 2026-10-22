# Requirement: Audit trail Workspace

## Tujuan

Workspace harus menyimpan jejak aktivitas pengguna dan perubahan data agar admin dapat melakukan penelusuran operasional dan investigasi dengan aman.

## Acceptance criteria

- Event menyimpan actor, waktu, modul, aksi, subject, request ID, IP address, user agent, device/browser/OS, HTTP method, dan route.
- Perubahan data menyimpan snapshot `old_values` dan `new_values` dalam JSON; password, token, file binary, dan secret tidak boleh masuk snapshot.
- Login berhasil, login gagal, logout, upload, update, archive, preview, permission, dan aksi domain yang sudah tersedia dicatat melalui service audit terpusat.
- Halaman audit berada di Core, hanya dapat dibuka oleh role `super_admin`, memiliki filter server-side untuk kata kunci, modul, pengguna, dan rentang tanggal, serta pagination.
- Edit dokumen menampilkan history dokumen pada side panel yang dapat diciutkan dan memuat waktu, actor, device, serta snapshot perubahan.
- Panel audit pada subject menggunakan pola sidebar horizontal reusable: default collapsed, tombol tab dengan ikon untuk expand/collapse, dan transisi ke samping; jangan menggunakan collapse vertikal ke atas/bawah.
- Detail subject dokumen memiliki link kembali ke daftar, tombol edit terotorisasi, preview file, metadata dokumen, serta panel audit horizontal.
- Tipe Dokumen memiliki detail terpisah dan form edit dengan panel audit subject yang sama, serta redirect create/update ke halaman detail.
- Audit event append-oriented dan tidak mengubah data bisnis; migration harus incremental dan mempertahankan event lama.

## Keputusan implementasi

Audit tetap memakai tabel `doc_audit_events` yang sudah menjadi source of truth runtime, sedangkan akses UI dan service menjadi bagian Core. Kolom konteks dan snapshot ditambahkan secara nullable agar event lama tetap dapat dibaca.
