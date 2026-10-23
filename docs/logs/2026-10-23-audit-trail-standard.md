# 2026-10-23 — Standard panel audit trail lintas modul

## Keputusan

Setiap panel audit trail pada modul baru wajib menggunakan permission granular milik modul dan policy subject scope. Contoh: `view_document_audit_trail` untuk Document Management dan `view_crm_audit_trail` untuk CRM.

## Aturan implementasi

- Akses melihat subject tidak otomatis memberikan akses audit trail.
- Policy harus memeriksa permission audit dan scope subject.
- Controller/service tidak boleh memuat event audit sebelum authorization berhasil.
- Blade hanya merender panel setelah authorization berhasil.
- Role assignment, requirement, acceptance criteria, dan test authorized/unauthorized wajib diperbarui bersama fitur.
- Audit global lintas subject/perusahaan tetap menggunakan akses yang lebih ketat sesuai kebutuhan, dan saat ini khusus `super_admin`; halaman ini bukan panel subject modul.
