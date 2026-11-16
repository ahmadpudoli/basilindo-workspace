# Pratinjau A4 cetakan reimbursement

- **Perubahan:** Seluruh lembar pratinjau (ringkasan, lampiran gambar, dan halaman PDF) memakai rasio A4 portrait. Halaman lampiran tidak lagi mengikuti tinggi konten; aturan cetak menjaga setiap lampiran pada area cetak A4. Tata letak ringkasan dipadatkan untuk menjaga tanda tangan persetujuan pada halaman pertama.
- **Requirement:** `docs/requirements/hris-claim-print-a4-preview.md`.
- **Verifikasi:** Pemeriksaan perubahan CSS dan `git diff --check`; belum dilakukan render browser/cetak aktual.
- **Risiko tersisa:** Konten ringkasan yang jauh lebih panjang dari data contoh masih dapat melampaui tinggi halaman pertama dan perlu ditinjau pada data nyata.
