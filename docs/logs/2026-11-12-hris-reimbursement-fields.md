# 2026-11-12 — Penataan field form reimbursement

## Perubahan

- Memindahkan Nomor PO ke panel kanan dan menampilkan/mengaktifkannya hanya untuk keperluan Project atau client.
- Menghapus field Lokasi kegiatan dari form.
- Memindahkan Keterangan umum ke panel kanan tepat setelah bagian Keperluan.
- Memperbarui requirement dan checklist.

## Verifikasi

- Meninjau perubahan Blade, perilaku enable/disable PO, requirement, dan checklist.
- Test suite dan build asset tidak dijalankan karena perubahan berfokus pada presentasi form.

## Risiko tersisa

- Field Nomor PO bersifat opsional. Field ini dinonaktifkan saat keperluan internal agar nilainya tidak ikut terkirim.
