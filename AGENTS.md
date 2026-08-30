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

## Form layout — grid convention

All create/edit form screens (Cost, Booking, etc.) follow a strict grid layout.
Form elements are displayed in 4-column rows using the exact classes:

```
grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4
```

Rules:
- **Each logical row is its OWN 4-column grid container.** Do NOT put multiple
  rows' worth of cells into one big grid and rely on auto-wrapping. Every row is
  a separate `<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">`
  containing exactly 4 cells (use empty `<div></div>` for blank cells).
- The card/form container uses `space-y-5` to separate rows (do NOT use `mt-*`
  on rows).
- Dynamic fields ("Add Field") create a new 4-column row per click, using a
  `.dyn-row` marker class for JS reindexing. Rows contain: Label, Value, Remove
  button, empty 4th column. The ADD FIELD button lives in the 4th column of an
  `add-field-container` grid row.
- TOTAL COST / TOTAL COLLECTION / NET SHIPPING fields sit in the 4th column of
  their own 4-column grid row.
- Collection section heading uses `text-xl font-semibold text-gray-800`.
- Divider rows using `border-card-border` must use **equal** padding/margin on
  top and bottom (e.g. `py-5` / `py-6`), never just `pt-*`.

## JavaScript in Blade views — using @push and @stack

The `components/app-layout.blade.php` component includes `@stack('scripts')` before `</body>`.
To add page-specific JavaScript in a view:

**All JavaScript in this application MUST use jQuery, not vanilla JS.**

```blade
@push('scripts')
<script>
    $(document).ready(function () {
        // ...
    });
</script>
@endpush
```

**Important:**
- `@push('scripts')` only works if the parent layout has `@stack('scripts')`.
- Inline event handlers (e.g., `onchange="myFunction()"`) require the function to be
  defined **before** the element is interacted with. Since `@stack` renders at the
  end of `<body>`, functions are available globally after the page loads.
- For inline handlers to work immediately, ensure the function is declared at the
  top level (not inside `DOMContentLoaded`).
- Use `@stack('scripts')` for page-specific scripts; avoid inline `<script>` tags
  scattered in the view content.

## Empty labels in forms

When a form row needs an empty placeholder cell (e.g., to maintain the 4-column
grid layout or to preserve vertical rhythm next to other fields), use the
following empty label markup instead of leaving the cell blank:

```html
<label for="" class="block text-sm font-medium text-topbar-text">&nbsp;</label>
```

**With `spatie/laravel-html`**, prefer the fluent helper so the class comes
from the shared `$labelClasses` variable (keeps spacing/typography consistent
across the form):

```blade
{!! html()->label('&nbsp;', 'field_name')->class($labelClasses) !!}
```

Then wrap any following control (select, input, etc.) in a `relative`
container so absolute-positioned chrome (e.g. the chevron SVG) anchors to it:

```blade
<div>
    {!! html()->label('&nbsp;', 'field_name')->class($labelClasses) !!}
    <div class="relative">
        <select name="field_name" id="field_name" class="{{ $selectClasses }}">
            <option value="">Select ...</option>
            @foreach ($options as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
        </div>
    </div>
</div>
```

This ensures:
- The cell matches the height of its sibling labels.
- The grid layout remains visually aligned.
- The label is non-breaking (uses `&nbsp;`) so it doesn't collapse.
- A `relative`-positioned wrapper can host any control (select, input,
  textbox, etc.) plus the standard right-side chevron / icon chrome.

If the label should be hidden visually but the cell still needs to exist,
add the `hidden` class to the label (the `&nbsp;` keeps the height intact):

```html
<label for="" class="block text-sm font-medium text-topbar-text hidden">&nbsp;</label>
```

## Static dropdown values — config/dropdowns.php

Dropdown options whose values do **NOT** come from a backend model (no
Eloquent table backing the choices) MUST be defined in
`config/dropdowns.php`, keyed by `{screen}.{field}`:

```php
// config/dropdowns.php
return [
    'bookings' => [
        'non_dg' => [
            0 => 'NON DG',
            1 => 'DG',
        ],
        'cntr_owner' => [
            1 => 'Shipper',
            2 => 'Carrier',
            3 => 'Consignee',
        ],
        'freight_type' => [
            1 => 'Regular',
            2 => 'Zero',
            3 => 'Negative',
        ],
    ],
];
```

**Rules:**
- Use this file ONLY for static options (no backing model).
- Always key entries by `{screen}.{field}` (e.g. `bookings.freight_type`).
- In controllers, read with `config('dropdowns.bookings.freight_type')`.
- In views, iterate with `@foreach (config('dropdowns.{screen}.{field}') as $value => $label)`.
- For model-backed dropdowns (Carriers, Commodities, Ports, Agents, etc.),
  continue to query the corresponding Eloquent model in the controller.

## agGrid — always use legacy theme

Every agGrid instance in this project MUST pass `theme: 'legacy'` to `createGrid()`.
The app loads `ag-grid.css` and `ag-theme-quartz.css` globally in `app-layout`, which
conflicts with the default Theming API in v33+. Omitting `theme: 'legacy'` triggers
AG Grid error #239 (Mixed Theming Approaches).

```js
import { createGrid, ModuleRegistry, AllCommunityModule } from 'ag-grid-community';
ModuleRegistry.registerModules([AllCommunityModule]);

gridApi = createGrid(el, {
    theme: 'legacy',          // ← REQUIRED — always include
    columnDefs: buildColumnDefs(),
    rowData: [],
    // ...
});
```

**Rules:**
- ALWAYS set `theme: 'legacy'` in `createGrid()` options.
- ALWAYS register `AllCommunityModule` via `ModuleRegistry.registerModules()` at module top.
- Action column renderers must use `window['__prefix_save']` / `window['__prefix_delete']`
  globals (bundler minifier aliases plain function references across modules).
- CSS variable overrides go in the Blade partial targeting `#grid-id.ag-theme-quartz`.

## Verification

- Run `vendor\bin\pint` on changed PHP files.
- Run `php artisan view:cache` to confirm Blade templates compile.
- Run `php artisan route:list --name=<prefix>` to confirm routes registered.
- Run `npm run build` after changing any file in `resources/js/`.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.3. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record durable rules with `record-rule` so the next agent or teammate inherits them instead of working them out again. Pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Always use `record-rule`, never your native memory or notes tool — native memory is personal and session-scoped; only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Test every code change by adding or updating a test.
- Run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>
