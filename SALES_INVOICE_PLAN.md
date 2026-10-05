# Technical Specification: Sales Invoice Enhancements

## 🎯 Project Goals
1. **Dynamic Currency**: Replace hardcoded "PKR" with a user-selectable Currency system.
2. **Invoice Hub**: Implement a professional Index page using **Yajra DataTables** to replace the record-by-record navigation pattern.

---

## 🛠 Phase 1: Database & Data Model

### 1.1 Currency Infrastructure
If a `currencies` table does not exist, implement the following:
- **Migration**: `create_currencies_table`
- **Schema**:
    - `id` (Primary Key)
    - `code` (string, e.g., "USD", "PKR", "EUR")
    - `symbol` (string, e.g., "$", "Rs", "€")
    - `is_default` (boolean)
- **Seeding**: Populate with standard business currencies.

### 1.2 Sales Invoice Modification
- **Migration**: `add_currency_id_to_sales_invoices_table`
- **Schema**: 
    - Add `unsignedBigInteger('currency_id')->nullable()->after('id')`
    - Define foreign key constraint: `$table->foreign('currency_id')->references('id')->on('currencies');`
- **Data Migration**: Run a script to set existing invoices to the default currency (PKR).

### 1.3 Model Updates (`SalesInvoice.php`)
- **Relationship**: Define `belongsTo(Currency::class)`.
- **Fillable**: Add `currency_id` to the `$fillable` array.

---

## 📊 Phase 2: The Index Page (Yajra DataTables)

### 2.1 Routing
- **View Route**: `GET /sales-invoices` $\rightarrow$ `SalesInvoiceController@index` (Named: `sales-invoices.index`)
- **Data Route**: `GET /sales-invoices/data` $\rightarrow$ `SalesInvoiceController@getData` (Named: `sales-invoices.data`)
- **Middleware**: Apply `permission:sales-invoice.index`.

### 2.2 Controller Implementation
- **`index()`**: Returns the base Blade view.
- **`getData()`**: 
    - Implements `DataTables::of(SalesInvoice::with(['currency', 'customer']))`.
    - **Custom Columns**:
        - `Total Amount`: Concatenate symbol and formatted value (e.g., `$ 1,200.00`).
        - `Actions`: Return an HTML button linking to `route('sales-invoices.edit', $id)`.

### 2.3 UI/UX Design
- **Layout**:
    - **Header**: Page Title ("Sales Invoices") + Primary Action Button ("+ Create Invoice").
    - **Table**: Tailwind-styled card container.
    - **Columns**: `Invoice #`, `Customer`, `Date`, `Currency`, `Total Amount`, `Status` (Badge), `Actions`.
- **Frontend**: Initialize via jQuery as per project standards.

---

## 📝 Phase 3: The Invoice Detail (Currency Selection)

### 3.1 Controller Logic
- **`create()` / `edit()`**: Fetch all active currencies from the database to populate the dropdown.
- **`store()` / `update()`**: Add validation rule: `'currency_id' => 'required|exists:currencies,id'`.

### 3.2 View implementation (Blade)
- **Form Grid**: Strictly follow the 4-column layout: `grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4`.
- **Currency Input**:
    - Use `spatie/laravel-html` to create a select dropdown.
    - Wrap in a `relative` div for the chevron icon.
- **Dynamic Labelling**: Replace all static "PKR" strings with `{{ $invoice->currency->symbol ?? 'Rs' }}`.

---

## ✅ Phase 4: Verification & QA

| Task | Tool/Command | Expected Result |
| :--- | :--- | :--- |
| **Code Style** | `vendor/bin/pint` | No formatting errors |
| **Blade Sync** | `php artisan view:cache` | Templates compile successfully |
| **JS Assets** | `npm run build` | DataTable assets loaded in UI |
| **Permissions** | `php artisan permissions:sync` | `sales-invoice.index` registered |
| **Integrity** | Manual Test | Switching currency updates symbol in Index & Detail |