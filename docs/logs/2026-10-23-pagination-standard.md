# 2026-10-23 — Standard limit pagination

## Perubahan

- Menetapkan standar opsi jumlah baris untuk daftar server-side: `10`, `25`, `50`, dan `100`.
- Menetapkan default standar `10` jika requirement fitur tidak menentukan default lain.
- Menyeragamkan validasi backend dan input filter pada Company, Vendor, Core Roles, Document, dan Document Type.
- Menyesuaikan fallback pagination AJAX Company ke `25`.
- Menyimpan aturan ini di `agents.md` dan requirement pagination Workspace.

## Verifikasi

- Tidak ada lagi whitelist backend `10, 15, 25, 50` atau `10, 20, 50` pada daftar yang memiliki filter jumlah baris.
- Seluruh input filter jumlah baris yang ada menampilkan empat opsi standar.
- `RoleListTest` lulus: 2 test dan 8 assertion.
- `npm.cmd run build` lulus.
- `git diff --check` lulus.
- Rangkaian `DocumentTypeFormViewTest` tidak dapat dijadikan verifikasi bersih karena database test persisten menolak akun `super_admin` kedua; database tidak di-reset sesuai aturan proyek.

## Risiko tersisa

- Beberapa daftar legacy masih memakai pagination tetap tanpa input jumlah baris. Daftar tersebut tidak memiliki filter limit yang perlu diseragamkan pada perubahan ini.
