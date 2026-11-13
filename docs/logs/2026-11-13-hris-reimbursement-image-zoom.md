# Zoom foto bukti reimbursement

## Perubahan

- Preview bukti foto di form reimbursement dan halaman detail kini memiliki kontrol perkecil, perbesar, dan sesuaikan.
- Kontrol hanya muncul untuk image yang dimuat pada viewer; PDF tetap menggunakan tampilan browser tanpa toolbar zoom tambahan.
- Toolbar responsif dan mendukung penggunaan tombol sentuh pada layar mobile.
- Requirement dan checklist HRIS diperbarui.

## Verifikasi

- Meninjau dua modal preview dan memastikan keduanya memakai viewer serta handler zoom yang sama.
- Test otomatis tidak dijalankan.

## Risiko tersisa

- Kontrol zoom memerlukan browser mengizinkan akses same-origin iframe image document; akses tetap menggunakan URL preview privat yang sama.
