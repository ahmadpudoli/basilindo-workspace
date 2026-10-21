# Form Edit Dokumen dan Metadata Tambahan

## Tujuan

Form edit dokumen harus memiliki susunan input yang konsisten dengan form tambah dokumen. File yang sudah tersimpan tetap ditampilkan sebagai lampiran read-only, sementara deskripsi menggunakan editor WYSIWYG yang sama.

Metadata tambahan ditampilkan dalam panel tersendiri di bawah informasi utama. Field panel mengikuti definisi metadata pada jenis dokumen yang dipilih. Jenis dokumen hanya boleh digunakan bersama perusahaan yang berlaku untuk tipe tersebut atau tipe global.

## Acceptance criteria

- Form edit menggunakan susunan lampiran, deskripsi, judul, perusahaan, jenis dokumen, metadata tambahan, dan metadata dokumen lainnya yang konsisten dengan form tambah.
- Lampiran pada form edit menampilkan nama file dan status read-only dalam panel ringkas.
- Panel metadata tambahan hanya muncul ketika jenis dokumen terpilih memiliki definisi field.
- Field metadata, tipe input, nilai wajib, dan nilai awal mengikuti definisi `required_fields` pada `DocumentType`.
- Backend menolak kombinasi perusahaan dan jenis dokumen yang tidak berlaku atau jenis dokumen yang tidak aktif.
- Build frontend, cache view, dan test view edit berhasil.
