# Filter daftar reimbursement dan cash advance

Tanggal: 2026-11-12

## Perubahan

- Filter status, jenis pengajuan, dan kategori menggunakan dropdown multi-select serta query server-side.
- Periode pengajuan memakai rentang tanggal `created_at`, default dari awal bulan berjalan sampai hari ini.
- Status `needs_revision` ditampilkan sebagai “Perlu revisi”.
- Kolom kategori merangkum kategori rincian secara unik pada setiap pengajuan.
- Ringkasan pilihan dropdown menampilkan jumlah opsi terpilih.

## Keputusan dan batasan

Status revisi relevan untuk proses reimbursement, tetapi workflow Claim saat ini belum memiliki aksi permintaan revisi dan pengajuan ulang. Status tersebut dapat difilter bila ada data, namun belum dihasilkan melalui aksi reviewer.

## Verifikasi

Pemeriksaan statis source dan diff dilakukan. Belum menjalankan test suite atau build.
