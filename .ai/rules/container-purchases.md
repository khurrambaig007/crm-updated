---
paths:
  - 'resources/views/container-purchases/**'
---

# Container Purchases

## Container purchase child DataTables — follow the Invoice pattern
All container purchase child DataTables (invoice, release, purchase, debit-note, po-cancel) use the standard yajra pattern, identical styling/config. Render with `{!! $childTable->table() !!}` in the Blade partial — do NOT hand-write a manual `<table>`/`<colgroup>`/two-row `<thead>`. The invoice table is the canonical reference; keep release and the others consistent with it (same table id convention `cp-{x}-table`, `display min-w-full text-sm` class, server-side yajra with minifiedAjax, `responsive: true`, identical `dom`/`language` parameters).
