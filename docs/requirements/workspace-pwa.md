# Requirement PWA Workspace

## Tujuan

Pengguna yang sudah login dapat memasang Basilindo Workspace sebagai aplikasi/shortcut pada perangkat mobile.

## Acceptance criteria

- Halaman authenticated memuat Web App Manifest dan mendaftarkan service worker; halaman login tidak memuat manifest atau kontrol instalasi.
- Manifest statis disajikan dengan MIME type JSON yang didukung browser.
- Manifest memiliki nama, warna tema, mode standalone, start URL dashboard, serta ikon PNG 192×192 dan 512×512 yang diwajibkan Chrome.
- Pengguna authenticated dapat memicu prompt instalasi pada browser yang mendukungnya.
- Browser tanpa prompt native (termasuk Safari iOS) menampilkan instruksi pemasangan yang sesuai.
- Fallback tombol memakai native dialog yang tampil pada mobile dan menjelaskan bila origin tidak secure.
- Service worker mendukung Web Push yang sudah ada, membuka tautan notifikasi dengan aman, dan tidak menyimpan halaman atau data bisnis privat di cache.
- Aplikasi tetap memakai autentikasi dan otorisasi Workspace saat dibuka sebagai aplikasi terpasang.

## Batasan dan asumsi

- Instalasi memerlukan secure context (HTTPS; `localhost` hanya dianggap aman pada perangkat yang sama). Mengakses server PC dari Android lewat HTTP/IP lokal tidak memenuhi syarat; gunakan HTTPS atau USB port forwarding untuk development.
- PWA awal tidak menyediakan mode offline untuk data bisnis.
