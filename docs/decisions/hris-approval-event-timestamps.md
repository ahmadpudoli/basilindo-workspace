# Timestamp event approval HRIS

- **Status:** Accepted - 2026-11-13
- **Decision:** Store `approval_events.created_at` in UTC. The `ApprovalEvent` model interprets database values as UTC and converts them to the application timezone for display.
- **Reason:** PostgreSQL `CURRENT_TIMESTAMP` uses the database session timezone (UTC in the local Docker stack), while HRIS request timestamps are created in `Asia/Jakarta`. Mixing the two shifts event times and misorders the timeline.
- **Consequence:** Every approval event writer must set UTC explicitly. Existing event rows written by the database default are interpreted as UTC.
