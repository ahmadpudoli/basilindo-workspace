# 2026-10-28 — Label bisnis parameter HRIS

## Perubahan

- Menambahkan nama bisnis pada kolom parameter Pengaturan HRIS sesuai label dropdown.
- Kode teknis seperti `hris.annual_leave_quota` tetap ditampilkan di bawah nama bisnis.

## Verifikasi

- Label yang digunakan mencakup kuota cuti, cut-off pengajuan, maksimum lembur, dan ambang approval final.
- Empty state dan struktur tabel tetap kompatibel dengan pagination yang ada.

## Risiko tersisa

- Penambahan parameter baru perlu memperbarui mapping label agar nama bisnisnya ikut tampil.
