# AGENTS.md

Project conventions and gotchas for working in this Laravel 13 CRM codebase.

## spatie/laravel-html — cross-check every method

The spatie/html elements expose a **limited** set of methods. Many common HTML
attributes are NOT available as fluent methods and calling them throws:

```
BadMethodCallException: Method Spatie\Html\Elements\Input::<method> does not exist.
```

Known missing methods (use `->attribute(...)` instead):
- `->step()` / `->min()` / `->max()` on number inputs
- `->readonly()` on inputs
- `->disabled()` on inputs (use `->attribute('disabled', 'disabled')`)

**ALWAYS cross-check every fluent method you call against the spatie/html API.**
When in doubt, use `->attribute('name', 'value')` which is always safe:

```blade
{!! html()->number('amount', $value)->class($inputClasses)->attribute('min', 0)->attribute('step', 'any')->attribute('readonly', 'readonly') !!}
```

After writing any Blade view that uses `html()->...`, run `php artisan view:cache`
to confirm it compiles before considering the task done.

## CRUD screens — record navigation pattern

New CRUD screens follow the Purchase Invoice pattern (NOT page-based pagination):

- Single edit/create view (no separate index list).
- `index()` redirects to the last record, or to `create` if none exist.
- Controller methods: `index`, `create`, `store`, `edit`, `update`, `destroy`, plus a `navigate()` JSON endpoint.
- View has First / Prev / New / Next / Last nav buttons + "Record X of Y" counter.
- Routes use permission middleware: `permission:{screen}.{action}`.

## Permissions — automated seeding

Permissions are auto-generated from `config('system.screens')` via `App\Services\PermissionSyncService`.

- Add a new screen to `config/system.php` (both `screens` and `navigation`).
- Run `php artisan permissions:sync` after building each new screen.
- A guarded boot-time sync in `AppServiceProvider` also runs automatically when a configured permission is missing.

## SweetAlert2 — reusable alerts

SweetAlert2 is loaded globally in `app-layout` and `guest-layout`. Use the global `window.Alerts` API (defined in `resources/js/sweetalert.js`) instead of native `alert()` / `confirm()`:

- `window.Alerts.success(message, title?)`
- `window.Alerts.error(message, title?)`
- `window.Alerts.validation(messages[], title?)` — lists validation errors
- `window.Alerts.confirm({ title, text, confirmText, cancelText, confirmColor })` — resolves `true`/`false`
- `window.Alerts.toast(message, type?)` — auto-dismissing toast

Flash messages (`session('status')` / `session('error')`) and validation errors are shown automatically via the `<x-alerts />` component, which is already included in both layouts. Do NOT add manual `@if (session(...))` alert blocks in views.

Delete forms use the `.delete-form` class (with optional `data-confirm`); the global submit handler in `sweetalert.js` shows the confirmation automatically.

## Cost screen form layout — grid convention

The Cost create/edit screens follow a strict grid layout. Form elements are
displayed in 4-column rows using the exact classes:

```
grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4
```

Rules:
- Each logical row is a 4-column grid; the card/form container uses `space-y-5`
  to separate rows (do NOT use `mt-*` on rows).
- Dynamic fields ("Add Field") create a new 4-column row per click, using a
  `.dyn-row` marker class for JS reindexing. Rows contain: Label, Value, Remove
  button, empty 4th column. The ADD FIELD button lives in the 4th column of an
  `add-field-container` grid row.
- TOTAL COST / TOTAL COLLECTION / NET SHIPPING fields sit in the 4th column of
  their own 4-column grid row.
- Collection section heading uses `text-xl font-semibold text-gray-800`.
- Divider rows using `border-card-border` must use **equal** padding/margin on
  top and bottom (e.g. `py-5` / `py-6`), never just `pt-*`.

## Verification

- Run `vendor\bin\pint` on changed PHP files.
- Run `php artisan view:cache` to confirm Blade templates compile.
- Run `php artisan route:list --name=<prefix>` to confirm routes registered.
- Run `npm run build` after changing any file in `resources/js/`.
