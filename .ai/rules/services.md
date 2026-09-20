---
paths:
  - app/Services/PermissionSyncService.php
---

# Services

## Guard against concurrent PermissionSyncService::sync() runs
PermissionSyncService::sync() is called from the AppServiceProvider boot guard (every web+console boot), the PermissionSeeder, and the permissions:sync command. Eloquent syncPermissions() does DELETE+INSERT without a lock, so two overlapping syncs race and throw "Duplicate entry ... role_has_permissions.PRIMARY" (SQLSTATE 1062). sync() is wrapped in Cache::lock('permissions:sync', 60) that skips when already held — keep any sync-within (role=permission grants) inside that lock body. Boot guard already swallows+reports errors.
