---
paths:
  - 'app/Http/Controllers/SalesInvoiceController.php, app/Models/SalesInvoice.php, app/DataTables/SalesInvoicesDataTable.php, resources/views/sales-invoices/**, app/Http/Requests/SalesInvoice/**'
---

# Sales Invoice

## Sales Invoice: code-based currency + DataTable index
sales_invoices.currency_code is a STRING code (max:10) validated against ExchangeRateService::availableCodes() — codes come from the keys of the latest currencies.exchange_rate JSON snapshot (currencies table stores date snapshots, NOT a currency definition list; no symbol/is_default). Never add a currency_id FK to it. Default 'PKR'. Status column uses config('dropdowns.sales_invoices.status') enum-style Rule::in validation; index badge = amber unpaid / emerald paid. Index is a Yajra DataTable hub (SalesInvoicesDataTable, `display min-w-full text-sm`, server-side, minifiedAjax to sales-invoices.data route) — index() renders it. All PKR labels are dynamic: Blade uses $currencyCode, JS reads data-currency-code from #sales-invoice-page.

## No record navigation on the sales-invoice screens
Per user decision (2026-10-07), the First/Prev/New/Next/Last record navigation strip was REMOVED from the sales-invoice create/edit screen (unlike purchase-invoices / container-activities / agent-receipt-payments, which still have theirs). The sales-invoices.navigate route, SalesInvoiceController::navigate() and navigationData() were deleted. Do not re-add record-nav or a navigate() endpoint for sales invoices.

## Invoice references a chosen BankAccount
sales_invoices.bank_account_id (nullable FK -> bank_accounts, nullOnDelete) is picked on the invoice form from BankAccount::orderBy('bank')->orderBy('account')->get(). The PDF payment page resolves $salesInvoice->bankAccount ?? BankAccount::current() (fallback = first row, kept for invoices created before selection existed). bank_accounts is multi-record CRUD (BankAccountsDataTable + container-size-shaped routes); BankAccount::current()/hasAccount() singleton semantics are gone — hasAccount() is deleted, current() is only the PDF fallback.
