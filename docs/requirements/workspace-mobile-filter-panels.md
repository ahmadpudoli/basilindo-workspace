# Panel Filter Responsif Workspace

## Requirement

Semua form filter berbasis GET pada area konten Workspace harus tetap tampil normal pada desktop dan berubah menjadi panel expandable pada layar mobile. Panel mobile berada dalam kondisi tertutup saat halaman pertama kali dibuka.

## Acceptance criteria

- Pada viewport maksimal 768px, form filter GET dibungkus otomatis dalam panel expandable.
- Kondisi awal panel mobile adalah collapsed dan dapat dibuka atau ditutup melalui tombol yang aksesibel.
- Halaman Persetujuan HRIS menggunakan label panel `Filter pengajuan`.
- Halaman lain menggunakan label standar `Filter data`, kecuali menyediakan `data-filter-title`.
- Desktop tidak dibungkus ulang dan mempertahankan layout filter yang sudah ada.
- Form yang tidak boleh dilipat dapat menggunakan atribut `data-no-mobile-collapse`.
- Implementasi tidak menggandakan event handler pada panel yang sudah memiliki markup collapsible.

