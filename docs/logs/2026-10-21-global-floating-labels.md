# 2026-10-21 — Floating label global dan alignment dropdown

## Perubahan

- Menambahkan floating label global untuk field form Workspace yang memiliki label langsung di atas input, select native, atau searchable dropdown, termasuk select yang disabled pada form edit.
- Label berubah warna dan bergerak halus saat field mendapat fokus.
- Memusatkan line-height value dropdown agar teks terlihat penuh dalam tinggi tetap 40px.
- Menetapkan jarak vertikal antar field form Workspace sebesar `.75rem` agar form tetap compact dan menjadi aturan dasar untuk form berikutnya.
- Merapatkan teks bantuan form ke input dengan margin atas `.1rem` dan line-height compact.
- Memberi jarak khusus `.5rem` sebelum panel metadata tambahan agar teks bantuan field terakhir tidak menempel ke panel.
- Menyamakan offset label dropdown custom dengan input native dan menyelaraskan padding horizontal footer tombol dengan body form.
- Menetapkan posisi awal label tag dengan offset `.72rem` sebagai acuan global untuk seluruh input dan dropdown Workspace, termasuk wrapper floating otomatis.
- Menyamakan containing block label native yang dibungkus JavaScript dengan kolom form agar posisi kiri konsisten dengan dropdown dan tag.
- Menggeser seluruh floating label ke offset global `1.2rem` agar sudut border input tetap terlihat jelas.

## Verifikasi

- `npm.cmd run build`
- `php artisan view:cache`
- `git diff --check`
