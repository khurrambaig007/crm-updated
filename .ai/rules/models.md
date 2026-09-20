---
paths:
  - app/Models/Booking.php
---

# Models

## Booking & CRO use SoftDeletes (children retained)
Booking and ContainerReleaseOrder use the SoftDeletes trait; destroy() is a soft delete. Related rows (otherInfo, equipment, revenues, costs) are intentionally NOT deleted on booking destroy — they are retained for reporting segregation. Number generators must count withTrashed() to avoid reusing numbers. DataTables use newQuery() so trashed rows are hidden automatically.
