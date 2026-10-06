# 2026-10-01 — Basilindo Document Hub UI

## Requirement dan acceptance criteria

- Halaman `/` harus menjelaskan fungsi manajemen dokumen untuk Finance, bukan project management.
- Panel `/admin` harus memiliki identitas Basilindo dan ringkasan dokumen yang relevan.
- Tidak ada referensi branding DewaKoding pada aplikasi `file-organizer/`.
- Dashboard wajib menghormati scope perusahaan saat menghitung dokumen, kasus review, dan audit activity.

## Perubahan

- Membuat landing page dark/cyan responsif dengan CTA masuk, fitur pencarian, verifikasi, bundling, dan kontrol akses.
- Mengganti dashboard Filament default dengan halaman ringkasan dokumen: total, siap, perlu perhatian, review, dokumen terbaru, dan audit activity.
- Mengubah nama aplikasi/panel menjadi Basilindo Document Hub dan memperbarui footer, README, serta license attribution.
- Menetapkan locale default contoh environment ke Bahasa Indonesia.

## Verifikasi

- Review statis route, view, provider, dan model scope dilakukan.
- Build asset dan test suite perlu dijalankan setelah environment database lokal aktif.

## Risiko tersisa

- Angka pada mockup landing page bersifat ilustratif; angka operasional hanya berasal dari dashboard terautorisasi.
- Tampilan akhir tetap perlu dicek pada browser untuk breakpoint mobile dan data kosong.
