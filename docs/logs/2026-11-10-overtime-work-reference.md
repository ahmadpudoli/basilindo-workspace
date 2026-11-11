# Referensi pekerjaan pada pengajuan lembur

## Perubahan

- Menambahkan referensi pekerjaan `project`, `opportunity`, atau `none` pada lembur `external_project`.
- Menambahkan `opportunity_id`, `work_reference_type`, `reference_name_snapshot`, dan `reference_status_snapshot` secara incremental pada tabel HRIS.
- Menambahkan validasi scope perusahaan dan permission `view_crm_opportunity` untuk opportunity.
- Menampilkan label `Sales` untuk stage won/delivered atau closing status closed; status lainnya ditampilkan sebagai prospek/tidak menjadi sales.
- Mempertahankan `project_id` untuk project mandiri dan opportunity yang sudah memiliki project.

## Verifikasi

- Migration `2026_11_10_000002_add_overtime_work_reference` berhasil dijalankan.
- `php artisan test tests/Feature/HrisWorkflowTest.php`: 13 test passed, 39 assertions.

## Risiko tersisa

- Result cache Pest gagal ditulis karena permission direktori vendor; tidak memengaruhi hasil test.
- `docs/decisions.md` memiliki encoding lama yang tidak dapat dipatch otomatis; keputusan implementasi dicatat di domain model dan log ini.
