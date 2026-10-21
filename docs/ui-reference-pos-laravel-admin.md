# Pedoman UI/UX dari `pos-laravel-admin`

## Status dan cakupan

Dokumen ini menetapkan `pos-laravel-admin/` sebagai **template/acuan UI/UX** untuk Basilindo Workspace. Folder tersebut bukan runtime, bukan sumber domain bisnis, dan bukan tempat menyalin konfigurasi aplikasi secara mentah.

Template harus dikonversi ke kebutuhan Basilindo Workspace: Bahasa Indonesia, domain Finance/document management, company/project scope, permission, audit trail, private object storage, serta modular monolith pada `app/workspace/`.

## Temuan audit template

- Template berbasis Laravel + Blade dengan layout bertingkat dan partial.
- Layout utama tersedia pada `resources/views/layouts/`, termasuk master umum dan layout business.
- Struktur UI dipisah ke partial seperti header, sidebar, footer, CSS, dan script.
- Halaman resource umumnya memisahkan halaman utama dan partial data tabel, misalnya `index.blade.php` dan `datas.blade.php`.
- Tabel menggunakan pagination Bootstrap dan query filter melalui request.
- Modal menggunakan Bootstrap `data-bs-toggle`/`data-bs-target`, dengan modal reusable pada folder components.
- JavaScript/jQuery digunakan untuk AJAX, status toggle, konfirmasi, notifikasi, theme toggle, dropdown, dan pencarian.
- Template memiliki dukungan light/dark theme, responsive table, action dropdown, badge status, checkbox bulk action, dan stack untuk CSS/JS/modal.
- Template saat ini bukan sumber dependency Basilindo: versinya berbeda dan memiliki domain POS, payment gateway, addon, serta konfigurasi yang tidak relevan.

## Prinsip adopsi untuk Basilindo

1. Pertahankan bahasa visual template: sidebar, header, card, table, filter bar, action dropdown, badge status, modal, empty state, responsive layout, spacing, dan theme.
2. Gunakan Blade sebagai default untuk halaman, layout, partial, form, tabel, pagination, dan modal.
3. Gunakan Livewire secara selektif bila interaksi dinamis memang memberi manfaat, misalnya filter tabel, pagination tanpa reload, dependent metadata fields, review workflow, atau progress bundle. Livewire hanya mengendalikan state/interaksi; business rule tetap berada di service, policy, request, dan job Laravel.
4. Bila komponen Blade biasa sudah cukup, jangan mengubahnya menjadi Livewire hanya untuk mengikuti template.
5. Semua label bisnis baru harus menggunakan Bahasa Indonesia. Nama class, route internal, table, enum, dan permission code menggunakan Bahasa Inggris yang konsisten.
6. Semua hasil tabel dokumen harus melewati authorization policy dan company/project scope sebelum dirender.
7. Modal yang berisi data sensitif harus mengambil data melalui endpoint/Livewire yang terotorisasi; jangan menanamkan data dokumen rahasia ke HTML halaman tanpa kebutuhan.
8. Gunakan komponen Blade reusable untuk pola visual yang berulang sesuai kebutuhan aktual.
9. Asset template boleh dijadikan referensi atau dikonversi secara selektif, tetapi jangan membawa kredensial, route POS, payment gateway, model, migration, atau data contoh ke Workspace.
10. Bootstrap/asset harus dikelola oleh pipeline frontend Workspace. Jangan menambahkan CDN atau asset global dari template tanpa keputusan dan pengujian.

## Pola implementasi yang disarankan

### Halaman resource

```text
resources/views/modules/{module}/{resource}/index.blade.php
resources/views/modules/{module}/{resource}/_table.blade.php
resources/views/modules/{module}/{resource}/_filters.blade.php
resources/views/modules/{module}/{resource}/_modal.blade.php
```

Controller menyiapkan query terotorisasi dan data awal. Blade merender layout. Livewire hanya dipakai pada `_table` atau komponen yang memang memerlukan reaktivitas.

### Tabel dan pagination

- Gunakan query builder/Eloquent dengan eager loading yang diperlukan.
- Terapkan authorization scope sebelum filter dan pagination.
- Gunakan pagination server-side sebagai default.
- Pertahankan query filter saat berpindah halaman (`withQueryString()` untuk halaman Blade atau state query Livewire).
- Sediakan loading, empty, validation, dan error state.
- Gunakan `wire:key` untuk baris jika tabel memakai Livewire.

### Modal

- Modal statis dan form sederhana boleh menggunakan Bootstrap murni.
- Modal yang datanya berubah per record dapat menggunakan Livewire atau endpoint JSON Laravel dengan authorization.
- Modal tidak boleh menjadi tempat business logic; submit tetap menuju action/controller/Livewire method yang memanggil application service.
- Setelah update/delete, tutup modal, tampilkan feedback, dan refresh data secara konsisten.

### Filter dan AJAX

- Filter yang dapat dibagikan/bookmark menggunakan query string.
- Interaksi kecil boleh menggunakan JavaScript/Bootstrap tanpa Livewire.
- Gunakan Livewire untuk state multi-field atau refresh tabel yang kompleks.
- Gunakan endpoint Laravel terotorisasi untuk kebutuhan AJAX; jangan membuat endpoint hanya untuk menghindari satu reload sederhana.

## Acceptance criteria penggunaan template

- UI Workspace terlihat konsisten dengan pola visual template, tetapi seluruh copy, navigasi, dan status mengikuti domain Basilindo.
- Tidak ada route, model, migration, permission, payment flow, atau kredensial POS yang masuk ke runtime Workspace.
- Halaman tabel memiliki pagination, filter, empty state, error state, dan authorization scope.
- Modal Bootstrap berfungsi pada halaman Blade biasa maupun di dalam komponen Livewire yang membutuhkan interaksi.
- Komponen Livewire tidak menyimpan aturan bisnis, melewati policy, atau mengakses object storage tanpa service terotorisasi.
- UI responsive pada viewport desktop dan mobile yang ditetapkan proyek.
- Test feature/presentation yang relevan memverifikasi permission, filter, pagination, modal action, dan failure path.

## Keputusan penggunaan Livewire

Basilindo tidak dipaksa menjadi 100% Blade statis. Standarnya adalah Blade + Bootstrap sebagai fondasi, dengan Livewire selektif untuk interaksi dinamis yang bernilai. Keputusan ini mempertahankan template UI sekaligus menghindari penulisan JavaScript/AJAX berulang untuk state tabel dan form yang kompleks.
