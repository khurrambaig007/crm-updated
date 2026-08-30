---
paths:
  - 'resources/js/**'
  - resources/js/app.js
---

# Js

## Inline view scripts must not reference $ — use bundled jQuery modules
Do NOT use $ or jQuery synchronously inside inline <script> blocks in Blade views — the Vite app.js bundle is an ES module and only sets window.$/window.jQuery AFTER the inline classic scripts run, so they throw "ReferenceError: $ is not defined". Page JS must live in a bundled jQuery module under resources/js/ (imported in app.js) using the pattern `jQuery(document).ready(function ($) { ... })`, with view data passed via data-* attributes read through jQuery (.data(). The containers-purchase screens use resources/js/container-purchase.js reading data-currency-rates from the page wrapper and the csrf token from the layout meta tag.

## select2 needs factory call + strict-mode _normalizeItem patch with Vite
select2 4.1.0 is CJS and needs two handling steps when bundled with Vite: (1) `import select2 from 'select2'; select2(window, $);` — the UMD export is a factory that must be called or `$.fn.select2` never attaches; (2) its internal `query()` maps results through `AjaxAdapter.prototype._normalizeItem` detached, which throws "can't access property 'container', this is undefined" under ESM strict mode — patch the prototype in app.js (`select2Amd.require('select2/data/ajax')`) to fall back to the prototype as `this` when the instance container is absent. Refresh after editing resources/js (Vite dev).
