---
paths:
  - app/Http/Controllers/ContainerPurchaseController.php
  - 'app/Http/Controllers/**'
---

# Controllers

## transactionsForPurchase matches prefix OR trailing digits
The select2 trans_no search (transactions-for-purchase) matches `trans_no LIKE q%` OR `LIKE %q` (prefix, plus last-digits suffix), NOT a bare `%q%` substring. A plain `%q%` LIKE matches too many rows (e.g. q=0001 also hits VRM0000000011). The endpoint is paginated: it accepts `page` (50 per page) and returns `pagination.more`, wired into Select2's `processResults`. Behavior is locked by ContainerPurchaseChildTablesTest::test_transactions_for_purchase_returns_partial_matches and ::test_transactions_for_purchase_paginates.

## Force JSON 422 on non-api AJAX endpoints
This app only renders JSON exceptions for api/* routes (`shouldRenderJsonWhen` in bootstrap/app.php). A plain ValidationException on non-api routes becomes a 302 redirect — breaking AJAX handlers and JSON-based tests. For AJAX endpoints outside api/*, validate then rethrow as `new HttpResponseException(response()->json(['message'=>..., 'errors'=>...], 422))` so the handler returns a real JSON 422.

## Container Purchase child screens are standalone sub-screens with row CRUD
The five child entities (ContainerPurchaseModel, Invoice, Release, DebiteNote, PoCancel) live on standalone screens under container-purchases/models|invoices|releases|debits|po-cancels, NOT tabs and NOT nested under a parent id. Each screen is add-form + DataTable of ALL rows. Child add/store routes reuse container_purchases.add/edit/delete permissions (no new screens in config/system.php). All child row endpoints (store/update/destroy) return JSON via validateChildForm; update/destroy are PATCH/DELETE 3-segment routes (container-purchases/models/{model} etc.). Never re-add per-partial @push('scripts'); child.blade.php owns the shared jQuery CRUD (edit populates via data-edit payload, appends _method=PATCH, restores add mode via data-store-url).
