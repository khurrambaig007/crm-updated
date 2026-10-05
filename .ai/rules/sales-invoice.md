---
paths:
  - 'app/Http/Controllers/SalesInvoiceController.php, app/Models/SalesInvoice.php, app/DataTables/SalesInvoicesDataTable.php, resources/views/sales-invoices/**, app/Http/Requests/SalesInvoice/**'
---

# Sales Invoice

## Sales Invoice: code-based currency + DataTable index
sales_invoices.currency_code is a STRING code (max:10) validated against ExchangeRateService::availableCodes() — codes come from the keys of the latest currencies.exchange_rate JSON snapshot (currencies table stores date snapshots, NOT a currency definition list; no symbol/is_default). Never add a currency_id FK to it. Default 'PKR'. Status column uses config('dropdowns.sales_invoices.status') enum-style Rule::in validation; index badge = amber unpaid / emerald paid. Index is a Yajra DataTable hub (SalesInvoicesDataTable, `display min-w-full text-sm`, server-side, minifiedAjax to sales-invoices.data route) — index() renders it, NOT the record-nav redirect; record-nav (First/Prev/New/Next/Last) still lives on the edit screen. All PKR labels are dynamic: Blade uses $currencyCode, JS reads data-currency-code from #sales-invoice-page.
