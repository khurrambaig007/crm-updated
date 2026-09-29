---
paths:
  - 'resources/views/**/*.blade.php'
  - 'resources/views/**'
---

# Views

## Never nest a delete form inside the main form
HTML forbids nesting <form> inside <form>. The browser drops the nested form's opening tag, so its @method('DELETE') hidden input leaks into the outer form. When both _method=PATCH (added by spatie for the main form) and _method=DELETE are POSTed, PHP keeps the LAST one (DELETE), so clicking Save routes to destroy and DELETES the record. Put action buttons (Delete/New/Save) as a sibling after closing the main form; the Save button submits via the form="cp-form" attribute. Guard edits with a DOM-based test asserting cp-form contains exactly one _method=PATCH and a standalone DELETE form routes to destroy.

## Spatie Html::form auto-adds CSRF token
`html()->form()` from spatie/laravel-html auto-injects `_token` and `_method` spoofing for non-GET forms (vendor/spatie/laravel-html/src/Html.php ~lines 296-299). Do NOT add `@csrf` inside spatie forms — you get it for free. A 405 Method Not Allowed on a spatie/POST form means the form `action` is wrong (e.g. `#`), not a CSRF problem.

## dompdf: never size logos with CSS max-*, and the body is inset past the margin
Two dompdf traps in the CRO PDF letterhead:

1. CSS `max-width`/`max-height` only ever SHRINK an image, so a tall logo collapses to a sliver (an 1800x2170 logo under `max-height: 100px` rendered at just 83x100). Compute explicit `width`/`height` in PHP instead — see `CompanyProfile::logoDisplaySize($maxWidth, $maxHeight, $maxUpscale = 4.0)`, which scales to a box, preserves the aspect ratio, and caps upscaling so a low-res logo is not blown into a blur. Pass the array to the view from the controller and guard the `<img>` on `$brandLogoSize` being non-null (a fake/placeholder file makes `getimagesize()` fail and silently drops the logo).

2. The dompdf body is inset ~34pt beyond the declared `margin` on BOTH sides, so "flush right" is NOT the page edge. Verify alignment against the real content box (the first clip path in the inflated content stream, e.g. x 52.016 -> 543.264 on A4), not against the page width. Use `table-layout: fixed` with explicit widths on BOTH cells, otherwise auto layout gives the first cell more than its share and squeezes the logo cell.

## dompdf stacks its own @page margin under the body margin
dompdf injects `@page { margin: 1.2cm }` from its own `lib/res/html.css` default stylesheet, and this stacks ON TOP of the view's `body { margin }`. So `body { margin: 24px }` actually yields a 52pt (18.4mm) edge inset, not 18pt — the inset is easy to misread as a CSS bug.

To control real page margins in a dompdf view you MUST declare `@page { margin: ... }` in the view's `<style>` and set `body { margin: 0 }`. The CRO PDF uses `@page { margin: 0.7cm }` (7mm, safely inside the unprintable margin of consumer printers while reclaiming ~11mm per edge). Note `config/dompdf.php` has no margin option, so `@page` is the only lever.

Verify against the PDF rather than by eye: inflate the page content stream and read the initial clip path for the real content box, and the image placement matrix (`w 0 0 h x y cm /Name Do`) for rendered size/position.
