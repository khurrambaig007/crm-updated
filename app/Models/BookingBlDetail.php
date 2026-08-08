<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['booking_id', 'bl_info_date', 'bl_info_agent', 'bl_info_booking_no', 'bl_info_sailing_date', 'bl_info_accounting_date', 'bl_info_carrier_mbl_no', 'bl_info_bl_number', 'bl_info_vessel_voyage_1', 'bl_info_vessel_voyage_2', 'bl_info_transshipment', 'bl_info_booking_si_status', 'booking_info_pol', 'booking_info_pofd', 'booking_info_pot_1', 'booking_info_pot_2', 'booking_info_shipper_bp', 'booking_info_cntr_owner', 'booking_info_agent_pofd', 'booking_info_agent_1', 'booking_info_agent_2', 'booking_info_consignee', 'booking_info_reference', 'release_instruction_date', 'release_instruction_status', 'release_instruction_type', 'release_instruction_description', 'delivery_order_doc', 'delivery_order_date', 'delivery_order_validity_date', 'delivery_order_agent', 'delivery_order_vessel_voyage', 'delivery_order_deliver_to', 'delivery_order_remarks', 'lock_info_pol_locked_to', 'lock_info_pol_locked_on', 'lock_info_pot_1_locked_to', 'lock_info_pot_1_locked_on', 'lock_info_pot_2_locked_to', 'lock_info_pot_2_locked_on', 'lock_info_pofd_locked_to', 'lock_info_pofd_locked_on', 'lock_info_locked_detail'])]
class BookingBlDetail extends Model
{
    protected function casts(): array
    {
        return [
            'bl_info_transshipment' => 'boolean',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
