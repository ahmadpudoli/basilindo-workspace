# ADR-015 — HRIS sebagai modul bertahap dengan parameter bisnis

- **Status:** Accepted — 2026-10-26
- **Keputusan:** HRIS diimplementasikan di dalam Workspace modular monolith,
  memakai User/Company Core dan migration owner Workspace. Fase pertama fokus
  pada employee, parameter, cuti, dan sakit.
- **Keputusan:** Kuota cuti memiliki default 12 hari per tahun, tetapi nilai
  bisnis disimpan sebagai parameter yang dapat berlaku per perusahaan/periode.
  Sakit tidak memakai kuota.
- **Keputusan:** Pengajuan cuti/sakit aktif tidak boleh memiliki tanggal overlap
  untuk karyawan yang sama. Status rejected, cancelled, expired, dan draft tidak
  memblokir tanggal.
- **Alasan:** Aturan perusahaan dapat berbeda dan dapat berubah tanpa migration;
  pemisahan fase menjaga approval, claim, dan notification tetap dapat dirancang
  sebagai workflow reusable.
- **Konsekuensi:** Service domain wajib membaca parameter dengan fallback aman,
  validasi overlap harus dilakukan di server dalam transaksi, dan setiap fase
  baru wajib menambah permission, policy, acceptance criteria, serta test.
