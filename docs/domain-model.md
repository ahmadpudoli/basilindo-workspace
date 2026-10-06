# Domain Model Awal

## Entitas inti

### Pemilik data

- `core/`: users, companies, vendors, projects, project membership, external identities, dan settings general.
- `file-organizer/`: document types, documents, document versions, relations, verification, bundles, dan audit event dokumen.
- `project-management/`: tickets, epics, ticket status/priority, notes, external access, notifications, dan workflow project management.
- `project-sso/`: issuer OIDC, client registration, subject, consent/session, dan entitlement SSO.

- `companies`: satu-satunya master seluruh entitas legal/bisnis; mencakup Group Basilindo, perusahaan internal yang berada di bawah Basilindo, serta client/vendor/partner eksternal. `classification` membedakan `internal` dan `external`, sedangkan `roles` menyimpan peran bisnis (`operating_company`, `client`, `vendor`, `partner`). Tidak ada master vendor terpisah untuk data baru.
- `parent_company_id`: hierarki Group Basilindo dan anak perusahaan.
- `users`: akun aplikasi; email bukan identifier lintas IdP; dimiliki `core`.
- `company_user`: keanggotaan user dan role/scope; satu user dapat memiliki banyak perusahaan/pihak. Hak akses dokumen dibatasi berdasarkan company ID yang terhubung pada user; dimiliki `core`.
- `projects`: proyek yang dijalankan oleh anak perusahaan internal; dimiliki `core`, sedangkan ticket/dokumen adalah extension domain aplikasi.
- `project_parties`: relasi project dengan perusahaan client/vendor/partner. Vendor adalah role perusahaan, bukan master entity terpisah. Model/tabel vendor lama dipertahankan sementara hanya untuk kompatibilitas migrasi.
- `document_parties`: pihak yang menerbitkan, menerima, membayar, atau menerima pembayaran dokumen.
- `document_types`: Invoice, PO, Receipt, Contract, Payment Proof, dan tipe custom.
- `documents`: metadata dokumen, owner scope, status lifecycle, tanggal, nomor referensi, nominal, checksum.
- `document_versions`: object key MinIO, size, MIME, checksum, uploader, dan version number.
- `document_relations`: relasi PO-invoice-receipt-payment atau relasi generic yang memiliki relation type.
- `verification_cases`: kasus review dengan status open/in_review/approved/rejected.
- `verification_items`: hasil rule, discrepancy, evidence, dan keputusan reviewer.
- `bundles`: snapshot filter/IDs, status job, object key, expiry, dan requester.
- `audit_events`: actor, action, subject, scope, request ID, metadata aman, timestamp.

## Aturan data

- Setiap document memiliki company scope dari perusahaan internal yang mengelola data; pihak client/vendor disimpan melalui `document_parties` dan/atau diturunkan dari `project_parties`.
- Reference number harus dinormalisasi untuk pencarian dan tetap menyimpan display value.
- Nominal menggunakan `numeric`, bukan floating point.
- Tanggal dokumen dipisahkan dari `created_at`.
- Duplicate detection minimum memakai company + document type + normalized reference number + checksum, dengan pengecualian yang terdokumentasi.
- Penghapusan memakai soft delete/quarantine; hard delete hanya lewat retention job yang terkontrol.
- Semua relation dan verification wajib menyimpan actor serta timestamps.
