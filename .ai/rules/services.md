---
paths:
  - app/Services/PermissionSyncService.php
  - app/Services/BookingSplitService.php
---

# Services

## Guard against concurrent PermissionSyncService::sync() runs
PermissionSyncService::sync() is called from the AppServiceProvider boot guard (every web+console boot), the PermissionSeeder, and the permissions:sync command. Eloquent syncPermissions() does DELETE+INSERT without a lock, so two overlapping syncs race and throw "Duplicate entry ... role_has_permissions.PRIMARY" (SQLSTATE 1062). sync() is wrapped in Cache::lock('permissions:sync', 60) that skips when already held — keep any sync-within (role=permission grants) inside that lock body. Boot guard already swallows+reports errors.

## Booking number sequencing lives in BookingSplitService and must skip split suffixes
All booking number generators (nextBookingSeq, nextBookingNo, nextReportingNo, nextSplitBookingNo) live in BookingSplitService; BookingController delegates. nextBookingSeq() MUST strip trailing "-NN" groups BEFORE pulling digits — a naive preg_replace('/\D/') turns AMSTSTDST000001-01 into 100001 and pushes every later booking up by 100000. Split children get number {parent}-NN and parent_booking_id, never a fresh sequence. Locked by tests/a_split_child_does_not_disturb_the_booking_number_sequence and test_split_numbering_increments_for_each_child.
