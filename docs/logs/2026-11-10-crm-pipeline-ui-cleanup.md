# Log — CRM pipeline UI cleanup

Status: selesai

## Perubahan

- Menghapus status account dari tabel ringkasan, daftar, detail, filter, form, dan sorting UI.
- Mempertahankan kolom database `status` untuk kompatibilitas data lama; tidak ada penghapusan kolom atau backfill.
- Mengganti dua kartu statistik Lead dan Opportunity menjadi satu kartu Pipeline Sales.
- Menghapus menu Lead dan Opportunity terpisah dari navigasi CRM.
- Menjelaskan pada halaman pipeline bahwa prospek dan opportunity dikelola dalam satu pipeline; label tipe tetap terlihat pada card.
- Memindahkan menu Konfigurasi CRM ke posisi paling bawah setelah Aktivitas.

## Verifikasi

- Pipeline existing tetap memakai `crm_opportunities` dan `record_type` untuk membedakan `lead` dan `opportunity`.
- Tidak ada migration atau perubahan data database.
- Perlu verifikasi browser pada dashboard CRM, daftar Account, detail Account, dan Pipeline setelah cache view dibersihkan.
- Verifikasi browser menunjukkan resource berakhir dengan Aktivitas, lalu submenu Stage Pipeline dan Team Sales.

## Risiko tersisa

- Tabel legacy `crm_leads` masih ada dan route CRUD-nya tetap tersedia untuk kompatibilitas permission/data lama, tetapi tidak lagi ditampilkan pada navigasi utama.
- Pipeline saat ini menampilkan item pada `crm_opportunities`; lead lokal legacy yang hanya berada di `crm_leads` belum diproyeksikan ke board tanpa keputusan migrasi domain tambahan.
