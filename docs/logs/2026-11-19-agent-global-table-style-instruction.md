# Instruksi penggunaan style tabel global

## Perubahan

- Menambahkan aturan kerja di `agents.md`: setiap tabel baru memakai `.workspace-table-card`, `.workspace-data-table`, dan footer `.workspace-list-pagination` bila ada pagination.
- Mengarahkan layout khusus melalui CSS custom property atau modifier, serta melarang duplikasi style dasar inline/per halaman.
- Checklist mencatat instruksi sebagai standar implementasi ke depan.

## Verifikasi

- Memeriksa keberadaan aturan pada instruksi root dan requirement UI pagination.
- `git diff --check` bersih.
