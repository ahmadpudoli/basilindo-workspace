# 2026-10-21 — Aturan preservasi data migration

Aturan proyek diperjelas: migration harus mempertahankan data lama. Perubahan schema dilakukan incremental dengan backfill dan kompatibilitas sementara bila diperlukan. Reset database hanya diperbolehkan pada database testing disposable.
