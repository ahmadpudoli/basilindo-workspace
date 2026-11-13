# 2026-11-12 — Dialog verifikasi bukti reimbursement

## Perubahan

- Mengubah dialog preview bukti nota menjadi layout dua panel: rincian biaya di kiri dan dokumen gambar/PDF di kanan.
- Panel rincian memuat tanggal nota/kegiatan, kategori, nominal, referensi nota, deskripsi, dan nama file.
- Layout beradaptasi menjadi vertikal pada layar kecil.
- Memperbarui requirement dan checklist fitur.

## Verifikasi

- Memeriksa perubahan Blade/JavaScript/CSS dan menjalankan `git diff --check`.

## Risiko tersisa

- Belum memverifikasi rendering langsung melalui browser.
