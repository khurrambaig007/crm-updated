---
paths:
  - 'tests/Feature/**'
---

# Feature

## Tests rendering app-layout need a complete CompanyProfile
Every authenticated screen is gated by EnsureCompanyProfileIsComplete, so any feature test that renders an app-layout page must seed a complete profile or it will get a 302 to the profile editor. Use the `Tests\Concerns\CreatesCompanyProfile` trait (`seedCompleteCompanyProfile()`). Also: tests that assert on permission counts must compare against `app(PermissionSyncService::class)->configuredPermissions()` with `assertEqualsCanonicalizing` (that list is in config order, so `assertSame` against a sorted DB pluck fails), never a hardcoded number that breaks whenever a screen is added.
