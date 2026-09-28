---
paths:
  - app/Models/Booking.php
---

# Models

## Booking & CRO use SoftDeletes (children retained)
Booking and ContainerReleaseOrder use the SoftDeletes trait; destroy() is a soft delete. Related rows (otherInfo, equipment, revenues, costs) are intentionally NOT deleted on booking destroy — they are retained for reporting segregation. Number generators must count withTrashed() to avoid reusing numbers. DataTables use newQuery() so trashed rows are hidden automatically.

## Split bookings are self-referential Booking rows sharing equipment aggregates
A split child is a full Booking row: inherits every header column (via getAttributes() minus NON_INHERITED) and gets a NEW booking_no ({parent}-NN, NN zero-padded to 2), its own reporting_no, is_split_booking=true, parent_booking_id, approved=false. Revenue and Cost lines are deliberately NOT copied — they stay on the parent so money is never duplicated. otherInfo IS copied. Equipment is type+quantity rows in booking_info_equipments, and (booking_id,size,type) is NOT unique, so splits aggregate by (size,type) before splitting.
