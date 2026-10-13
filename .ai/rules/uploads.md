---
paths:
  - resources/views/**/*.blade.php
  - resources/js/**
  - app/Http/Requests/**
---

# File & image uploads

Every file/image upload field in this application is built the same way. The
reference implementation is `resources/views/stamps/_image-field.blade.php`
(markup + scoped CSS) with `resources/js/stamps.js` (behaviour). Copy that shape;
do not ship a bare `<input type="file">` with `file:` pseudo-element styling.

## Where the pieces live

- **Markup** — a shared partial included by both the create and edit screens, so
  the two cannot drift. Pass the existing record in when editing.
- **Behaviour** — a bundled jQuery module under `resources/js/`, imported in
  `app.js`, scoped by a `data-*` hook on the wrapper. Never an inline
  `<script>` block in Blade (the Vite bundle only sets `window.$` after inline
  classic scripts run).
- **Validation** — the Form Request is the authority. The client-side checks
  below are immediate feedback, never the only check.

## Anatomy

```html
<div data-file-upload data-has-file="false" data-dragging data-removed>
    <!-- current file, edit only: thumbnail + filename + "Remove" checkbox -->
    <div data-upload-existing>…</div>

    <div class="upload-drop relative rounded-xl border-2 border-dashed …">
        <input type="file" data-upload-input class="absolute inset-0 … opacity-0">
        <div data-upload-idle>   icon tile · primary instruction · hint · format pill </div>
        <div data-upload-preview> thumbnail · filename · size · Clear button </div>
    </div>

    <p data-upload-feedback></p>   <!-- transient feedback -->
    <p>static hint (formats + limit)</p>
</div>
```

The input stays a **real `<input type="file">`** stretched over the zone at
`opacity-0`. That keeps it keyboard reachable and makes it submit natively with
the form; do not replace it with a div + manual POST.

## Visual states

| State | Treatment |
| --- | --- |
| Idle | dashed `border-card-border`, `bg-page-bg`, icon tile in `bg-primary-100 text-primary-600` |
| Hover | border → `primary-300`, surface → `primary-50/40` |
| Dragging | border → `primary-500`, `bg-primary-50`, soft ring via `color-mix`, icon scales 1.08 |
| Selected | idle panel hidden, preview panel shown |
| Removed | current-file block `opacity-0.45` + `grayscale(1)` and a "Will be removed on save" tag |
| Invalid | feedback line in `text-red-600` |

Use the semantic colour tokens (`bg-card-bg`, `ring-card-border`,
`text-topbar-muted`, `primary-50…900`) rather than raw colours so light and dark
themes both work.

## Behaviour

- `dragenter`/`dragover` set `data-dragging`; `dragleave`/`dragend` clear it.
  `preventDefault` on both or the browser navigates to the dropped file.
- `drop` rebuilds a `DataTransfer`, assigns `input.files`, and renders the
  preview — a dropped file must behave exactly like a picked one.
- Choosing a file **clears any remove flag**; checking remove **clears a
  pending selection**. The form must never be able to say remove *and* replace.
- Clear resets the input, the preview, the feedback line and `data-has-file`.
- Reject early with a readable sentence: wrong type, or over the limit (quote
  the real size, e.g. "That image is 3.0 MB. The limit is 2 MB.").

## State styling — read this before writing the CSS

Tailwind resolves two display utilities by **stylesheet order, not
class-attribute order**, so JS that toggles `hidden` against `flex` /
`inline-flex` on the same element silently fails. Drive visibility from data
attributes with hand-written CSS instead; attribute selectors beat utilities
regardless of order:

```css
[data-file-upload] .upload-idle { display: flex; }
[data-file-upload][data-has-file="true"] .upload-idle { display: none; }
```

Never write two `class` attributes on one element — the parser keeps the first
and silently drops the second, which kills the styling with no error.

## Server-side contract

- Store the **path on the `public` disk** in a string column, never a blob.
  Resolve it through the model (`imageUrl()`, `imagePath()`) so the DataTable
  and the view share one rule; always guard with `is_file()` before rendering,
  because a path can outlive its file.
- Keep the mime allowlist and size cap identical on both sides. Image fields:
  `['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']` (2 MB), matching the
  Company Profile logo.
- The image is `required` on create and `nullable` on update, so someone can
  rename a record without re-uploading the file.
- **Delete the file on replace, on remove, and on destroy.** Deleting the row
  while leaving the file behind accumulates orphans silently.
- Replacing wins over the remove flag when both arrive in one request.
- A thumbnail column in a DataTable needs `editColumn` + `rawColumns` and a
  blank-value fallback, e.g. an em dash.

## Before calling it done

Exercise in a browser, not by reading the markup: idle → pick a file → preview
and decoded thumbnail; drag highlight on and off; Clear; an over-size file; a
wrong-type file; check Remove (image dims, tag appears); pick a replacement
(Remove un-checks, dim clears); then save and confirm the old file is gone from
disk.