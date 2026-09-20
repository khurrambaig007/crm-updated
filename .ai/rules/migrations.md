---
paths:
  - 'database/migrations/**'
---

# Migrations

## down() must drop FKs per-column, matching the names up() created
In MySQL, each ->constrained()/->foreign() in up() creates its OWN single-column FK, named {table}_{column}_foreign. down() must drop them one-per-line with matching names. NEVER call $table->dropForeign([...multiple columns...]) unless up() literally created a composite key via $table->foreign([...columns...]). A composite-name drop of separate FKs fails with SQLSTATE 1091 "Can't DROP ... check that column/key exists" and aborts any migrate:rollback/refresh mid-way, leaving the DB half-migrated. Applies especially to 2026_08_20_000000 and 2026_08_31_215315 (both already fixed).
