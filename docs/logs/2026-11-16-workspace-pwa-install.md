# PWA Workspace — 2026-11-16

## Perubahan

- Menambahkan manifest dengan ikon PNG 192×192 dan 512×512 untuk memenuhi kriteria installability Chromium, serta metadata manifest hanya pada halaman authenticated.
- Menggunakan file `manifest.json` agar Nginx menyajikannya dengan MIME type `application/json` standar.
- Menambahkan tombol instalasi setelah login, prompt native browser, dan petunjuk Add to Home Screen untuk Safari iOS/browser tanpa prompt.
- Menambahkan service worker network-only untuk request GET tanpa cache halaman privat; worker juga menangani Web Push dan klik notifikasi.
- Mengganti fallback petunjuk instalasi dari Bootstrap modal menjadi native dialog agar tetap muncul pada browser mobile, dengan peringatan secure context untuk Android.

## Acceptance criteria

- Pengguna yang login mendapatkan manifest, service worker, serta aksi/petunjuk instalasi.
- Halaman login tidak memuat manifest maupun kontrol instalasi.
- Data Workspace tidak tersimpan di cache service worker.
- Tombol instalasi di Android memberi tahu pengguna bila halaman dibuka lewat HTTP non-secure.

## Verifikasi

- `npm.cmd run build` berhasil; Vite memberi peringatan ukuran chunk yang sudah ada (PDF worker lebih dari 500 kB).
- Belum menguji instalasi pada perangkat Android/iOS; perlu membuka aplikasi melalui secure context.

## Risiko tersisa

- Perangkat Android yang mengakses server lokal melalui HTTP/IP memerlukan HTTPS atau USB port forwarding sebelum Chrome dapat menawarkan instalasi.
- Jika Chrome masih memakai metadata lama, muat ulang halaman authenticated sebelum mencoba instalasi lagi.
- Bila Chrome masih menyimpan manifest lama, muat ulang halaman authenticated atau periksa Application → Manifest di DevTools.
