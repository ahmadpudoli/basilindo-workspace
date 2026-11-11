# Form master penomoran Core

## Requirement

Form tambah, edit, dan filter master penomoran memakai komponen global
`.workspace-floating-field`, dengan kontrol sebelum label dan pasangan `id`/`for`.
Format nomor tetap berupa input teks utuh; bantuan token berada di bagian paling
bawah area input form tambah maupun edit, setelah Counter dan sebelum tombol simpan,
bukan di dalam label atau menutupi border input. Tidak ada penggantian label lewat
JavaScript atau CSS posisi khusus halaman.

## Acceptance criteria

- Form tambah memakai dua kolom sama lebar pada desktop: Perusahaan–Jenis form
  pada baris pertama dan Format nomor–Counter pada baris kedua; pada mobile satu kolom.
- Semua input dan select yang terlihat memakai floating label global.
- Form tambah dan edit menyediakan bantuan token yang dapat dibuka setelah seluruh field input
  tanpa modal bertumpuk, serta contoh format yang dapat dibaca pada layar kecil.
- Error validasi server muncul di bawah field terkait dan nilai input dipertahankan.
- Filter, sorting, pagination 10/25/50/100, edit, aktivasi, dan counter terakhir
  tetap tersedia; counter terakhir dirender langsung oleh Blade.
- Tidak ada perubahan data, schema, format tersimpan, authorization, atau aturan counter.
