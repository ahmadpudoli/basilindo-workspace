# Pembersihan legacy project-management — 2026-10-01

## Status

Source cleanup selesai. Penghapusan tabel PostgreSQL menunggu persetujuan eksplisit
karena bersifat destruktif terhadap data yang ada.

## Perubahan source

- Mempertahankan projects dan project_members sebagai katalog/scope dokumen.
- Menghapus consumer ticket, epic, project notes, external dashboard, dan notifikasi
  khusus ticket dari model, Resource, Page, Widget, import/export, policy, event,
  listener, view, dan test.
- Menghapus projects.ticket_prefix dari model/form/test target.
- Menyederhanakan dashboard/resource proyek menjadi metadata proyek dan jumlah dokumen.
- Memperbarui README agar tidak lagi mendeskripsikan task tracker.
- Menjadikan migration historis pembuatan external access sebagai no-op agar fresh
  migration tidak membuat credential atau menulis data sensitif.

## Verifikasi

- php artisan route:list --path=admin --no-ansi berhasil; route ticket/epic/external
  dashboard tidak lagi terdaftar.
- Database lokal diperiksa: tabel legacy kosong kecuali ticket_priorities berisi 3
  seed default.
- php artisan test belum dapat dinyatakan lulus setelah perubahan; runner Windows
  menggantung pada proses PHP yatim dan perlu dijalankan ulang setelah migration cleanup
  disetujui.

## Pending

- Buat dan jalankan migration yang menghapus tabel legacy serta kolom projects.ticket_prefix.
- Hapus permission legacy dari tabel Shield.
- Jalankan ulang test suite dan verifikasi db:show setelah cleanup.

## Hasil akhir

- Migration 2026_10_01_000001_remove_legacy_project_management_schema berhasil
  dijalankan pada database lokal.
- Jumlah tabel turun dari 39 menjadi 29.
- Tabel legacy project-management sudah tidak ada dan kolom projects.ticket_prefix
  sudah dihapus.
- Permission legacy ticket/notification dibersihkan; tersisa 10 permission domain.
- Test suite: 20 passed, 50 assertions.
- route:list admin tidak lagi menampilkan route ticket, epic, notification, atau
  external dashboard.
- npm.cmd run build terhambat oleh EPERM saat Vite menghapus asset lama di
  public/build; tidak ada error kompilasi PHP/JS yang dilaporkan.

## Perbaikan SSO

- Memperbaiki view persetujuan OAuth Passport di `project-sso` dengan mengirim
  hidden `auth_token` pada form Izinkan dan Tolak.
- Penyebab 403 adalah Passport membandingkan token POST yang kosong dengan token
  otorisasi yang disimpan di session.
- Cache compiled view SSO berhasil dibersihkan dengan `php artisan view:clear`.
