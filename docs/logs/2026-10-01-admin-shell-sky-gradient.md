# 2026-10-01 — Admin shell sky-blue gradient

## Perubahan

- Menambahkan background gradasi biru langit pada body admin di `project-sso` dan `file-organizer`.
- Menambahkan gradasi yang serasi pada sidebar tanpa mengubah warna primary Filament.
- Menambahkan garis tepi kanan yang tegas, bayangan halus, serta penyesuaian dark mode pada sidebar.
- Mengubah arah gradasi dari kanan bawah ke kiri atas, dengan warna semakin pudar menuju kiri atas.
- Menurunkan opacity garis tepi sidebar menjadi 50% dan melembutkan shadow.
- Menyatukan gradasi sebagai satu background halaman penuh yang fixed saat scroll.
- Membuat sidebar transparan agar gradasi tidak terpisah antara body dan sidebar.
- Membatasi area gradasi hingga sekitar 58% diagonal sebelum menyatu dengan warna dasar.

## Verifikasi

- Build asset Vite dijalankan untuk kedua aplikasi.

## Risiko tersisa

- Tampilan akhir tetap perlu dicek pada browser untuk breakpoint mobile dan kondisi sidebar collapse.
