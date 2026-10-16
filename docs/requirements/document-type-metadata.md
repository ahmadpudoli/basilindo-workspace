# Requirement metadata tambahan tipe dokumen

## Tujuan

Admin dapat mendefinisikan metadata tambahan untuk setiap jenis dokumen dengan tipe data dan kewajiban yang eksplisit. Form dokumen harus mengikuti konfigurasi tersebut.

## Acceptance criteria

- Form create/edit Jenis Dokumen menampilkan baris metadata dengan `Nama field`, `Tipe`, dan `Mandatory`.
- `Tipe` hanya menyediakan `string`, `numeric`, `date`, dan `datetime`.
- `Mandatory` hanya menyediakan `Ya` dan `Tidak`.
- Field bertipe `date` memakai datepicker; field bertipe `datetime` memakai date-time picker.
- Field dengan mandatory `Ya` wajib diisi; field dengan mandatory `Tidak` boleh kosong.
- Nilai metadata divalidasi ulang pada service upload, termasuk numeric, date, dan datetime.
- Konfigurasi lama berbentuk `nama_field => tipe` tetap dibaca sebagai metadata mandatory untuk kompatibilitas migrasi.
- Konfigurasi metadata yang tersimpan sebagai daftar baris juga dinormalisasi agar seluruh field, termasuk optional, tetap dirender.
- Baris konfigurasi tanpa nama field dianggap tidak valid dan harus diperbaiki di Jenis Dokumen; sistem tidak menebak nama field bisnis.

## Bentuk data

```json
{
  "invoice_number": {"type": "string", "mandatory": true},
  "invoice_date": {"type": "date", "mandatory": false},
  "approved_at": {"type": "datetime", "mandatory": false}
}
```
