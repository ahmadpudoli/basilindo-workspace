# Standarisasi KPI Ringkasan Cuti & Sakit — 2026-11-20

## Perubahan

- Mengganti panel KPI dengan komponen global `workspace-overtime-summary-panel` dan `workspace-overtime-summary-card`.
- Mengurangi padding dan tinggi minimum agar panel lebih ringkas; memakai dekorasi lingkaran standar di sisi kanan.
- Membuat panel mobile collapsible dengan kondisi awal tertutup melalui perilaku panel mobile Workspace.
- Menempatkan toggle sebagai kontrol terpisah dan panel KPI global langsung di bawahnya, tanpa membungkusnya dalam kartu collapsible tambahan. Panel selalu tampil pada desktop dan hanya disembunyikan sebelum dibuka pada mobile.
- Menggunakan shell global `.workspace-mobile-panel` untuk toggle ringkasan, menyamakan border/latar dengan panel filter, serta memusatkan perilaku collapse mobile KPI di `app.css`.
- Menambahkan padding luar pada body KPI ketika dibuka di mobile agar panel gradasi tidak menempel pada border shell collapsible.
- Memperbarui requirement dan checklist.

## Verifikasi

- Belum menjalankan test atau build.

## Risiko tersisa

- Tampilan desktop/mobile belum diperiksa langsung di browser.
