<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingBlContainerInfo;
use App\Models\BookingInfoEquipment;

/**
 * Keeps BL Info > Container Info in step with the booking's equipment lines.
 *
 * Design rules, both deliberate:
 *
 *  1. One Container Info row per equipment line, keyed by equipment_id. The
 *     grid is sized by equipment, not by the number of physical boxes, so a
 *     line of 34 stays a single row.
 *
 *  2. Syncing is null-only. Once a row exists it belongs to the user, so a
 *     later equipment edit backfills only the fields still empty and never
 *     overwrites anything they typed. This is the same contract as
 *     BlInfoController::backfillFromBooking().
 */
class BlContainerInfoSync
{
    /**
     * Sync a single equipment line, creating its row the first time.
     */
    public function syncForEquipment(BookingInfoEquipment $equipment): void
    {
        $equipment->loadMissing('booking', 'containerSize', 'containerType');

        // Container Info is only reachable from an approved booking, so there is
        // nothing to build rows for while the booking is still a draft.
        if (! $equipment->booking?->approved) {
            return;
        }

        $row = BookingBlContainerInfo::firstOrNew([
            'booking_id' => $equipment->booking_id,
            'equipment_id' => $equipment->id,
        ]);

        foreach ($this->attributesFromEquipment($equipment) as $column => $value) {
            if ($value === null) {
                continue;
            }

            if ($row->exists && $row->{$column} !== null) {
                continue;
            }

            $row->{$column} = $value;
        }

        $row->save();
    }

    /**
     * Sync every equipment line on the booking. Used when a booking is
     * approved, and anywhere else a full rebuild is wanted.
     */
    public function syncForBooking(Booking $booking): void
    {
        if (! $booking->approved) {
            return;
        }

        $booking->loadMissing('equipments.containerSize', 'equipments.containerType');

        foreach ($booking->equipments as $equipment) {
            $this->syncForEquipment($equipment);
        }
    }

    /**
     * Drop the row belonging to a deleted equipment line. The equipment_id FK
     * also cascades at the database level, so this is belt-and-braces, kept at
     * the application layer to make the one-row-per-line contract explicit.
     * Rows a user added by hand have a null equipment_id and are never touched.
     */
    public function removeForEquipment(BookingInfoEquipment $equipment): void
    {
        BookingBlContainerInfo::where('equipment_id', $equipment->id)->delete();
    }

    /**
     * Map an equipment line onto the container columns that can be prefilled.
     * Everything else is deliberately absent so the user fills it in.
     *
     * @return array<string, mixed>
     */
    private function attributesFromEquipment(BookingInfoEquipment $equipment): array
    {
        $size = trim(implode(' ', array_filter([
            $equipment->containerSize?->size,
            $equipment->containerType?->name,
        ])));

        return [
            'container_info_cntr_owner' => $equipment->booking?->cntr_owner,
            'container_info_size' => $size === '' ? null : $size,
            // Carried across verbatim: the grid row stands for the whole line,
            // so it carries the line's full figures rather than a share of them.
            'container_info_gross_weight' => $equipment->gross_weight,
            'container_info_cbm' => $equipment->cargo_volumn,
            'container_info_packages' => $equipment->packages,
            'container_info_unit' => $equipment->unit,
        ];
    }
}
