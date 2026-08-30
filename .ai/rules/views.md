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
