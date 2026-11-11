# ADR-014 — Boundary integrasi Odoo read-only

- **Status:** Accepted — 2026-11-09
- **Keputusan:** MVP Integration Workspace membaca database PostgreSQL Odoo secara read-only melalui adapter terisolasi. Projection dan external reference disimpan di database Workspace.
- **Alasan:** pengembang tunggal dapat memenuhi kebutuhan HRIS tanpa membangun addon/API Odoo terlebih dahulu, sementara domain HRIS tidak bergantung pada struktur tabel Odoo secara langsung.
- **Batasan:** adapter PostgreSQL adalah solusi MVP dan bergantung pada struktur internal Odoo. Tidak boleh melakukan write ke Odoo. API/connector Odoo menjadi jalur evolusi jika integrasi perlu write-back, webhook, atau kontrak publik yang stabil.
- **Alternatif ditunda:** API Odoo/connector custom ditunda sampai kebutuhan write-back atau real-time terbukti.
