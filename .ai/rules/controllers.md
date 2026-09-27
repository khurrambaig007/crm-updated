---
paths:
  - app/Http/Controllers/ContainerPurchaseController.php
  - 'app/Http/Controllers/**'
  - app/Http/Controllers/BookingController.php
---

# Controllers

## transactionsForPurchase matches prefix OR trailing digits
The select2 trans_no search (transactions-for-purchase) matches `trans_no LIKE q%` OR `LIKE %q` (prefix, plus last-digits suffix), NOT a bare `%q%` substring. A plain `%q%` LIKE matches too many rows (e.g. q=0001 also hits VRM0000000011). The endpoint is paginated: it accepts `page` (50 per page) and returns `pagination.more`, wired into Select2's `processResults`. Behavior is locked by ContainerPurchaseChildTablesTest::test_transactions_for_purchase_returns_partial_matches and ::test_transactions_for_purchase_paginates.

## Force JSON 422 on non-api AJAX endpoints
This app only renders JSON exceptions for api/* routes (`shouldRenderJsonWhen` in bootstrap/app.php). A plain ValidationException on non-api routes becomes a 302 redirect — breaking AJAX handlers and JSON-based tests. For AJAX endpoints outside api/*, validate then rethrow as `new HttpResponseException(response()->json(['message'=>..., 'errors'=>...], 422))` so the handler returns a real JSON 422.

## Container Purchase child screens are standalone sub-screens with row CRUD
The five child entities (ContainerPurchaseModel, Invoice, Release, DebiteNote, PoCancel) live on standalone screens under container-purchases/models|invoices|releases|debits|po-cancels, NOT tabs and NOT nested under a parent id. Each screen is add-form + DataTable of ALL rows. Child add/store routes reuse container_purchases.add/edit/delete permissions (no new screens in config/system.php). All child row endpoints (store/update/destroy) return JSON via validateChildForm; update/destroy are PATCH/DELETE 3-segment routes (container-purchases/models/{model} etc.). Never re-add per-partial @push('scripts'); child.blade.php owns the shared jQuery CRUD (edit populates via data-edit payload, appends _method=PATCH, restores add mode via data-store-url).

## Container purchase invoice uses dedicated table
The Container Purchase child "Invoice" screen is backed by the dedicated `container_purchase_invoices` table / `ContainerPurchaseInvoice` model (created after the generic `invoices` table was retired). The old `invoices` table was dropped. Debit notes reference invoices via `debite_notes.invoice_id` → `container_purchase_invoices.id`. Update tests that reference `\DB::table('invoices')` to `container_purchase_invoices`.

## withCurrency derives code/rate/total server-side
`withCurrency()` derives `currency_code`, `currency_exchange_rate`, and `total_amount` SERVER-SIDE from the submitted `currency` code (looked up in the latest currencies.exchange_rate JSON via `exchangeRateFor()`), so these are always saved correctly even if client JS misses the hidden inputs. `currency_code` is forced from the code, NOT from the client's hidden currency_code field. Locked by ContainerPurchaseChildStoreTest::test_invoice_store_resolves_currency_fields_server_side.

## PO Cancel child screen now links invoice + parent purchase
The container purchase PO Cancel child screen stores only 3 fields and links to the invoice and the parent Container Purchase via FKs: po_cancels has invoice_id (FK -> container_purchase_invoices) and container_purchase_detail_id (nullable FK -> container_purchases), plus transaction_date. doc_no and trans_no were dropped. The form uses the cp-transno-select (container_purchase_detail_id) for Transaction No., an invoice_id select populated from $invoices, and a transaction_date date input. The PoCancel model (PoCancel.php) exposes invoice() and containerPurchase() relations, and PoCancelsDataTable shows Invoice No. and Transaction No. resolved from those relations.

## Booking auto-numbering (booking_no + reporting_no)
Booking numbers are ALWAYS auto-generated on store and preserved on update. booking_no = {AMS prefix from config('dropdowns.bookings.booking_prefix')}{POL port_code}{POFD location_code}{6-digit padded global seq}. reporting_no = {B prefix from config('dropdowns.bookings.reporting_prefix')}-{count for current year}/{2-digit year} (resets each calendar year). Both scans use withTrashed() so soft-deleted rows keep their numbers occupied. Sequencing must be DB-agnostic (pluck + preg_replace digits, never MySQL SUBSTRING/REGEXP) because tests run on SQLite :memory:. Both columns have UNIQUE indexes.

## Never pass $request->string() into spatie assignRole/syncRoles
$request->string('role') returns an Illuminate\Support\Stringable. spatie getStoredRole() only accepts string/int/Role, so passing a Stringable throws TypeError "Return value must be of type Spatie\Permission\Contracts\Role, Iterator returned" (HasRoles.php:500). Always pass a plain string: use $request->validated('role') (role is validated as nullable|string|exists:roles,name) or $request->input('role').

## Approving a booking auto-creates one Container Release Order
BookingController::approve() wraps the approved toggle + CRO sync in DB::transaction. On approve it does $booking->containerReleaseOrder()->updateOrCreate(['booking_id' => ...], $this->croAttributesFromBooking($booking)) so there is exactly ONE CRO per booking and re-approval re-syncs changed booking fields instead of duplicating. Un-approving never touches the CRO (retained for audit). No permission gate on CRO creation — it is a side effect of the bookings.edit-gated route. Field mapping: commodity->commodity_id, non_dg->dg_status (both 0=NON DG/1=DG), pol->pol_id, pofd->pofd_id, cntr_owner copied as-is (1/2/3 match on both screens); notes left null. Soft-deleted CROs are excluded from the lookup, so they are re-created. Locked by 4 tests in BookingStoreUpdateTest.
