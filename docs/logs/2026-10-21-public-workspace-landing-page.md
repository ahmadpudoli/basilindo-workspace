# Public Workspace Landing Page

## Perubahan

- Mengubah route `/` menjadi landing page publik Basilindo Workspace.
- Menambahkan hero section, CTA login, preview dashboard, modul Company/CRM/Project Management/Document Management, arah HRIS, alur kerja, dan CTA penutup.
- Menambahkan layout responsif serta animasi ringan berbasis CSS tanpa asset eksternal atau dependency baru.
- Menjaga route login lokal dan tidak menampilkan data privat.

## Verifikasi

- Acceptance criteria landing page diperbarui di docs/requirements/public-workspace-landing-page.md.
- Blade view cache dan Vite production build dijalankan.

## Catatan lanjutan daftar perusahaan

- Menambahkan kolom klasifikasi dengan badge.
- Menyatukan pencarian, pagination, dan sorting kolom dalam satu query server-side yang dipanggil melalui AJAX tanpa reload halaman.
