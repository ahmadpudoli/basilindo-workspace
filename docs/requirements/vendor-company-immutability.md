# Immutability perusahaan yang memiliki relasi vendor

## Acceptance criteria

- Perusahaan yang menjadi master vendor atau memiliki relasi vendor tidak dapat menghapus peran `vendor`.
- `entity_type`, `classification`, dan `status` perusahaan tersebut tidak dapat diubah.
- Form edit menonaktifkan field yang terkunci dan tetap mengirim nilai lama.
- Backend menolak request yang mencoba mengubah atau menghapus nilai terkunci.
- Perusahaan tanpa relasi vendor tetap dapat diedit sesuai aturan umum.
