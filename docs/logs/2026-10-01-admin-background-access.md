# 2026-10-01 — Konsistensi background admin Basilindo Access

## Requirement dan acceptance criteria

- Background setelah login pada File Organizer dan project SSO mengikuti atmosfer halaman Basilindo Access.
- Glow cyan/blue harus lembut, tidak menurunkan kontras tabel, form, navigasi, atau dashboard.
- Mode gelap tetap memiliki background yang layak dibaca.

## Perubahan

- Mengganti gradient diagonal lama pada theme Filament kedua aplikasi dengan kombinasi radial glow cyan/blue dan dasar `#f7fafc`.
- Menambahkan fallback gradient gelap untuk mode dark.
- Mempertahankan container utama transparan dan sidebar transparan agar background menyatu penuh.
- Menghapus blok CSS duplikat pada theme SSO agar satu aturan menjadi sumber kebenaran.

## Verifikasi

- Kedua theme menggunakan aturan background yang sama.
- `php artisan view:cache` berhasil pada kedua aplikasi.
- `npm.cmd run build` berhasil pada `project-sso`.
- `file-organizer` berhasil mentransformasi 59 modul, tetapi tahap penulisan asset gagal karena file asset lama terkunci proses lokal (`EPERM`). Percobaan tanpa mengosongkan output juga terblokir pada penulisan asset.

## Risiko tersisa

- Visual akhir tetap perlu dicek pada browser setelah asset Vite `file-organizer` dapat ditulis ulang, khususnya pada mode dark dan sidebar mobile.
