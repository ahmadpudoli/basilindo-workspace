# Perbaikan form master penomoran Core

## Posisi bantuan di akhir form

- Memindahkan bantuan pola pada form tambah ke baris penuh setelah Counter dan
  sebelum tombol Simpan master penomoran.
- Memindahkan bantuan pola pada modal edit ke akhir modal-body, setelah Counter
  dan sebelum footer tombol Batal/Simpan perubahan.
- Mempertahankan isi bantuan, ID, aria-describedby, dan susunan dua kolom.
- Requirement dan checklist diperbarui; urutan markup kedua form diperiksa.

## Penyesuaian dua kolom

- Menyamakan seluruh empat field form tambah ke `col-md-6`: Perusahaan–Jenis form
  dan Format nomor–Counter. Mobile tetap satu kolom mengikuti breakpoint Bootstrap.
- Requirement dan checklist diperbarui. Verifikasi struktur memastikan keempat
  field memakai kolom yang sama; perubahan hanya class layout Blade.

## Perubahan

- Mendokumentasikan requirement dan acceptance criteria pada
  `docs/requirements/core-numbering-rule-form.md`.
- Menyamakan seluruh input/select tambah, edit, dan filter dengan struktur global
  `.workspace-floating-field`, kontrol sebelum label, dan pasangan `id`/`for` unik.
- Menghapus skrip penggantian label, CSS posisi khusus, dan modal bantuan bertumpuk.
  Bantuan token menggunakan elemen `details` di bawah input pada form tambah/edit.
- Memperlebar format nomor, memakai font monospace, mempertahankan nilai input lama,
  dan menampilkan error server per field.
- Merender counter terakhir langsung di Blade dan menyesuaikan colspan empty state.
  Filter mempertahankan sorting; tautan edit/tutup mempertahankan query daftar.
- Memperbarui checklist. Tidak ada perubahan schema, aturan nomor, atau authorization.

## Verifikasi

- Build produksi Vite: lulus.
- Render Blade menggunakan data dummy tanpa akses database: lulus untuk tambah,
  edit dengan error validasi, dan daftar kosong. Pemeriksaan DOM mencakup seluruh
  floating wrapper, pasangan label/input, ID unik, bantuan format, error server,
  colspan tabel, serta ketiadaan script/style override halaman.
- `git diff --check`: lulus (terdapat peringatan line-ending pada perubahan lain).
- Belum ada pemeriksaan visual browser interaktif; perubahan hanya presentasi Blade.
  Tidak menjalankan migration atau mereset database.

## Catatan lingkungan

Sandbox menolak penulisan direktori view dan temporary Vite; penulisan serta build
dilakukan melalui eskalasi yang disetujui otomatis. Perubahan lain yang sudah ada
di working tree dipertahankan.
