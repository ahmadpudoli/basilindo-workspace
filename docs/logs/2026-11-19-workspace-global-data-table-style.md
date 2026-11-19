# Style global tabel data Workspace

## Perubahan

- Memindahkan style presentasi dasar tabel ringkasan reimbursement dari CSS inline halaman ke `workspace/resources/css/app.css`.
- Menambahkan class pakai ulang `.workspace-table-card` dan `.workspace-data-table` untuk container, header, baris, hover, border, serta lebar tabel minimum yang dapat dikonfigurasi lewat CSS custom property.
- Menjaga aturan khusus kolom reimbursement di modifier `.workspace-data-table--claims`; pagination tetap memakai `.workspace-list-pagination` yang sudah global.
- Halaman ringkasan reimbursement memakai class global sebagai contoh implementasi.

## Cara pakai

Gunakan `class="card workspace-table-card"` pada container dan `class="table align-middle workspace-data-table"` pada tabel. Tambahkan `--workspace-table-min-width` atau modifier konteks bila perlu mengatur scroll dan kolom khusus.

## Verifikasi

- Meninjau pemindahan selector dan memastikan class baru dipakai oleh partial tabel ringkasan reimbursement.
- `git diff --check` bersih. Test/build tidak dijalankan.
