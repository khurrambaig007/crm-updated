---
paths:
  - 'resources/views/components/*layout*.blade.php'
---

# Components

## Brand the app from CompanyProfile, never from config('app.name')
App identity comes from the CompanyProfile singleton, not config('app.name'). Both layouts resolve `$profile = CompanyProfile::current()` in a top `@php` block and read everything through the model API (`displayName()`, `displaySubtitle()`, `logoUrl()`, `faviconUrl()`). Do not inline `Storage::disk('public')->url(...)` in views, and do not call `CompanyProfile::missingFields()` from a layout that already has `$profile` - use `$profile->missingFieldsOn()` so branding + the gate cost one query. config('app.name')/config('app.subtitle') remain as fallbacks for the no-profile state.
