# HRIS setting: modal konfirmasi arsip

## Perubahan

- Menghapus penggunaan `window.confirm()` pada tombol Arsipkan parameter HRIS.
- Tombol kini memakai modal konfirmasi Workspace yang konsisten dengan aksi
  konfirmasi lain di aplikasi.

## Verifikasi

- Memastikan form arsip memiliki `data-confirm` dan `data-confirm-action`.
- Memastikan tidak ada lagi `onclick="return confirm(...)"` pada halaman
  Pengaturan HRIS.
