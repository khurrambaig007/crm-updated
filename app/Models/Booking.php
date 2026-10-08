<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['booking_no', 'reporting_no', 'approval_no', 'reference_no', 'booking_date', 'carrier', 'cntr_owner', 'sailing_date', 'commodity', 'non_dg', 'vessel_voyage', 'pol', 'pofd', 'pot_1', 'pot_2', 'shipper_bp', 'agent_pol', 'agent_pofd', 'agent_1', 'agent_2', 'act_shipper', 'freight_type', 'freight_type_sub', 'consignee', 'srr', 'services', 'services_sub', 'thru_bl', 'booking_status', 'is_split_booking', 'parent_booking_id', 'approved'])]
final class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'sailing_date' => 'date',
            'thru_bl' => 'boolean',
            'is_split_booking' => 'boolean',
            'approved' => 'boolean',
        ];
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    // Commodity model + commodity field = bookingCommodity (avoids column collision)
    public function bookingCommodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class, 'commodity');
    }

    public function vesselVoyage(): BelongsTo
    {
        return $this->belongsTo(VesselVoyage::class, 'vessel_voyage');
    }

    // Pol model + pol field = polPol
    public function polPol(): BelongsTo
    {
        return $this->belongsTo(Pol::class, 'pol');
    }

    // Pod model + pofd field = podPofd
    public function podPofd(): BelongsTo
    {
        return $this->belongsTo(Pod::class, 'pofd');
    }

    // Pol model + pot_1 field = polPot1
    public function polPot1(): BelongsTo
    {
        return $this->belongsTo(Pol::class, 'pot_1');
    }

    // Pol model + pot_2 field = polPot2
    public function polPot2(): BelongsTo
    {
        return $this->belongsTo(Pol::class, 'pot_2');
    }

    public function shipperBp(): BelongsTo
    {
        return $this->belongsTo(ShipperBp::class, 'shipper_bp');
    }

    public function agentPol(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_pol');
    }

    public function agentPofd(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_pofd');
    }

    public function agent1(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_1');
    }

    public function agent2(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_2');
    }

    public function otherInfo(): HasOne
    {
        return $this->hasOne(BookingOtherInfo::class);
    }

    public function containerReleaseOrder(): HasOne
    {
        return $this->hasOne(ContainerReleaseOrder::class);
    }

    public function blDetail(): HasOne
    {
        return $this->hasOne(BookingBlDetail::class);
    }

    public function equipments(): HasMany
    {
        return $this->hasMany(BookingInfoEquipment::class);
    }

    public function revenues(): HasMany
    {
        return $this->hasMany(BookingRevenue::class);
    }

    public function costs(): HasMany
    {
        return $this->hasMany(BookingCost::class);
    }

    public function parentBooking(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_booking_id');
    }

    public function splitBookings(): HasMany
    {
        return $this->hasMany(self::class, 'parent_booking_id')->orderBy('booking_no');
    }

    public function hasSplitBookings(): bool
    {
        return $this->relationLoaded('splitBookings')
            ? $this->splitBookings->isNotEmpty()
            : $this->splitBookings()->exists();
    }
}
