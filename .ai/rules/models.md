---
paths:
  - app/Models/Booking.php
  - app/Models/BookingInfoEquipment.php
---

# Models

## Booking & CRO use SoftDeletes (children retained)
Booking and ContainerReleaseOrder use the SoftDeletes trait; destroy() is a soft delete. Related rows (otherInfo, equipment, revenues, costs) are intentionally NOT deleted on booking destroy — they are retained for reporting segregation. Number generators must count withTrashed() to avoid reusing numbers. DataTables use newQuery() so trashed rows are hidden automatically.

## Split bookings are self-referential Booking rows sharing equipment aggregates
A split child is a full Booking row: inherits every header column (via getAttributes() minus NON_INHERITED) and gets a NEW booking_no ({parent}-NN, NN zero-padded to 2), its own reporting_no, is_split_booking=true, parent_booking_id, approved=false. Revenue and Cost lines are deliberately NOT copied — they stay on the parent so money is never duplicated. otherInfo IS copied. Equipment is type+quantity rows in booking_info_equipments, and (booking_id,size,type) is NOT unique, so splits aggregate by (size,type) before splitting.

## Equipment `unit` is a unit code, not a number
`booking_info_equipments.unit` holds KGS / MT / LBS and is sourced from `config('dropdowns.bl_info.units')` so the Equipments grid and the BL Detail screen agree. It was an int with an integer cast, an `integer` validation rule and an `agNumberCellEditor`; text was impossible to enter and the only storable values were meaningless integers. Never reintroduce a numeric editor, `(int)` cast, or `parseInt` on it — the grid cell is an `agSelectCellEditor` fed from `config.units` in `#equipment-grid-config`.
