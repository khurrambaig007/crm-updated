# Booking Screen – Layout & Flow

This document describes the Booking module's UI structure and how data flows between
its screens, tabs, sub-tabs, and the backend.

## 1. Booking List (Index)

- **Route:** `bookings.index` (permission: `bookings.view`)
- **View:** `resources/views/bookings/index.blade.php`
- **DataTable:** `App\DataTables\BookingsDataTable` (`#bookings-table`)
- **Columns:** Booking #, Reference #, Booking Date, Sailing Date, Carrier, **Approved** (badge), Actions (Edit / Delete).
  - The **Approved** column renders a green "Approved" badge or gray "Pending" badge
    driven by the `bookings.approved` boolean column.
- Row actions are gated by `bookings.edit` / `bookings.delete` (super admin always sees them).

## 2. Create Booking

- **Route:** `bookings.create` → store via `POST bookings.store` (`bookings.add`)
- **View:** `resources/views/bookings/create.blade.php`
- **Tabs:** Basic Info · Other Info · Message (top-level tab nav).
  - **Basic Info** is the only functional form (the booking header record).
  - **Other Info** and **Message** tabs are placeholders on Create (they have no submit
    on create — they are fully editable only after the booking exists, via the Edit
    screen's PATCH endpoints `bookings.other-info.update` / `bookings.message.update`).
- **No Equipment / Revenue / Cost sub-tabs appear on Create** — those grids are
  Edit-only (they need an existing `booking_id` to attach rows to).

## 3. Edit Booking

- **Route:** `bookings.edit` → header via `PATCH bookings.update`; sub-data via dedicated endpoints
- **View:** `resources/views/bookings/edit.blade.php`
- **Tabs:** Basic Info · Other Info · Message.

### 3.1 Basic Info tab
- `PATCH bookings.update` form for the booking header (Booking #, Approval #, Carrier,
  Commodity, Vessel/Voyage, POL/POFD/POTs, Agents, Shipper/BP, Freight Type, Consignee, etc.).
- After the Basic Info form closes, the view `@include('bookings._booking-details')`,
  which renders the **Booking Detail** card with three sub-tabs.

### 3.2 Booking Detail card (`_booking-details.blade.php`)
Sub-tab nav: **Equipments · Revenue · Cost**.

| Sub-tab | View | Grid data | Backend endpoints |
|---------|------|-----------|-------------------|
| Equipments | `_equipment.blade.php` | Booking containers | `bookings.equipments.*` |
| Revenue | `_revenue.blade.php` | `booking_revenues` | `bookings.revenues.*` |
| Cost | `_cost.blade.php` | `booking_costs` | `bookings.costs.*` |

Each grid is an ag-Grid (legacy theme) initialised in its own JS module:

- `resources/js/booking-equipment-grid.js` (globals `BEG` / `__beg_save` / `__beg_delete`)
- `resources/js/booking-revenue-grid.js` (globals `BRG` / `__brg_save` / `__brg_delete`)
- `resources/js/booking-cost-grid.js` (globals `BCG` / `__bcg_save` / `__bcg_delete`)

#### Revenue grid
- **Form row** (above the grid): Charges, Size, Type, Quantity, Mrg, Rate, Currency, Ex. Rate + Add/Clear buttons.
- **Grid columns:** Actions, Id, Charges, Size, Type, Quantity, Mrg, Rate, Amount,
  Currency, Ex. Rate, Amount In Dollar, Freight Type, Pa Party / TPA Agent, Hide, Remarks.
- **Formulas:** `Amount = Quantity × Mrg × Rate`; `Amount In Dollar = Amount / Ex. Rate`.
- Selecting a **Currency** auto-fills **Ex. Rate** from the latest `Currency` exchange-rate
  table (`config.rates[code]`); editing the Rate/currency recalculates the row.

#### Cost grid
- Exact copy of the Revenue grid **except** the **Rate** column is replaced by **Cost**,
  and an extra **Slot Term** column (sourced from `slot_terms`, stored as `slot_term` id,
  displayed as the `term` label) sits **right after the Type column**.
- **Formulas:** `Amount = Quantity × Mrg × Cost`; `Amount In Dollar = Amount / Ex. Rate`.

#### Row behaviour (all three grids)
- Each row has a **Save (✓)** and **Delete (🗑)** action rendered by `actionsRenderer`.
  - Save → AJAX `POST` (new) or `PATCH` (existing) the row; on success the row turns
    from red → default and the dirty flag is cleared.
  - Delete → SweetAlert2 confirm → AJAX `DELETE`.
- **Row colours** (`getRowStyle`):
  - Red `#fef2f2` → unsaved new row (no `id`).
  - Amber `#fefce8` → dirty row (`__dirty` set on any cell edit, cleared after save).
  - Default → saved/clean.
- Numeric columns use a 2-decimal `numberFormatter`.

### 3.3 Totals & Approve (end of Booking Detail card)
Below the three sub-tabs, a footer card shows three **read-only** inputs plus a toggle:

- **Revenue** (`#booking-revenue-total`) — auto-sum of the Revenue grid `Amount` column.
- **Cost** (`#booking-cost-total`) — auto-sum of the Cost grid `Amount` column.
- **Net** (`#booking-net-total`) — `Revenue − Cost`.
- **Approve** (`#booking-approve-btn`) — toggle button gated by `bookings.edit`, reflecting
  the booking's `approved` state. Click → `PATCH bookings.approve` flips `approved`;
  the button label/colour and the index **Approved** badge update accordingly.

These totals are computed by `resources/js/booking-totals.js`
(`window.computeBookingTotals()`), which reads `window.__revenueGridApi` and
`window.__costGridApi` (exposed by the two grid modules) and recomputes on grid init,
`onRowDataUpdated`, and after every row save/delete. The Approve button's AJAX handler
lives in the same file.

## 4. Data Flow Summary

```
Bookings index (DataTable)
        │  click "Edit"
        ▼
Bookings edit  ── Basic Info form ── PATCH bookings.update ──► Booking (header)
        │
        └─ Booking Detail card
              ├─ Equipments grid ── AJAX ──► bookings.equipments.{store,update,destroy}
              ├─ Revenue grid    ── AJAX ──► bookings.revenues.{store,update,destroy}
              ├─ Cost grid       ── AJAX ──► bookings.costs.{store,update,destroy}
              └─ Totals (Revenue/Cost/Net) + Approve toggle
                    └─ Approve ── PATCH bookings.approve ──► Booking.approved
                                                                  │
                                                                  ▼
                                           reflected as Approved badge on index
```

## 5. Permissions

`bookings.view / add / edit / delete` (auto-synced from `config('system.screens')`).
Revenue/Cost/Equipment sub-grid writes reuse the `bookings.edit` gate; Approve also
requires `bookings.edit`. The Bookings index Approved column is read-only display.

## 6. Key files

- Controllers: `app/Http/Controllers/BookingController.php`
- Models: `Booking`, `BookingRevenue`, `BookingCost`, `BookingInfoEquipment`
- DataTables: `app/DataTables/BookingsDataTable.php`
- Views: `bookings/{index,create,edit}.blade.php`, `bookings/_booking-details.blade.php`,
  `bookings/_revenue.blade.php`, `bookings/_cost.blade.php`, `bookings/_equipment.blade.php`
- JS: `resources/js/booking-revenue-grid.js`, `bookings-cost-grid.js`,
  `booking-equipment-grid.js`, `booking-totals.js` (all imported in `resources/js/app.js`)
- Migration for approval flag: `database/migrations/*_add_approved_to_bookings_table.php`
