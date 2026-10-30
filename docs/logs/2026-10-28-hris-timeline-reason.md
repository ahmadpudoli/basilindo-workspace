# Alasan pada timeline pengajuan HRIS

## Perubahan

- Timeline detail cuti/sakit menampilkan alasan pada aktivitas “Pengajuan dibuat”.
- Komentar approval ditampilkan dengan label “Keterangan” pada seluruh panel timeline.
- Penyesuaian diterapkan pada view detail modern, detail approval, dan view detail legacy.

## Verifikasi

- Seluruh view timeline HRIS diperiksa dan PHP/view expressions tetap valid.
- Resource detail dipindahkan ke template Blade terformat normal untuk mencegah
  parse error `unexpected token endif` pada view lama.
