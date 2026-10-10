<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\BlContainerInfoSync;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row of the BL Info > Container Info grid.
 *
 * Rows are created automatically from the booking's equipment lines and then
 * belong to the user: see {@see BlContainerInfoSync}, which only
 * ever fills fields that are still null.
 */
#[Fillable([
    'booking_id',
    'equipment_id',
    'container_info_cntr_owner',
    'container_info_size',
    'container_info_container_no',
    'container_info_seal_no',
    'container_info_seal2_no',
    'container_info_load_type',
    'container_info_gross_weight',
    'container_info_net_weight',
    'container_info_cbm',
    'container_info_packages',
    'container_info_unit',
    'container_info_temperature',
    'container_info_slot_operator',
    'container_info_slot_operator_ts1',
    'container_info_slot_operator_ts2',
    'container_info_imo_class',
    'container_info_un_number',
    'container_info_remarks',
])]
final class BookingBlContainerInfo extends Model
{
    use HasFactory;

    protected $table = 'booking_bl_container_infos';

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(BookingInfoEquipment::class);
    }
}
