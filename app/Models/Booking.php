<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['booking_no', 'approval_no', 'reference_no', 'booking_date', 'carrier', 'cntr_owner', 'sailing_date', 'commodity', 'non_dg', 'vessel_voyage', 'pol', 'pofd', 'pot_1', 'pot_2', 'shipper_bp', 'agent_pol', 'agent_pofd', 'agent_1', 'agent_2', 'act_shipper', 'freight_type', 'freight_type_sub', 'consignee', 'srr', 'services', 'services_sub', 'thru_bl', 'booking_status', 'is_split_booking'])]
class Booking extends Model
{
    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'sailing_date' => 'date',
            'thru_bl' => 'boolean',
            'is_split_booking' => 'boolean',
        ];
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    public function vesselVoyage(): BelongsTo
    {
        return $this->belongsTo(VesselVoyage::class);
    }

    public function pol(): BelongsTo
    {
        return $this->belongsTo(Pol::class);
    }

    public function pofd(): BelongsTo
    {
        return $this->belongsTo(Pod::class);
    }

    public function pot1(): BelongsTo
    {
        return $this->belongsTo(Pol::class, 'pot_1');
    }

    public function pot2(): BelongsTo
    {
        return $this->belongsTo(Pol::class, 'pot_2');
    }

    public function shipperBp(): BelongsTo
    {
        return $this->belongsTo(ShipperBp::class);
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
}
