<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['trans_id', 'agent_id', 'agent_id_2', 'container_no', 'ca_doc_no', 'activity', 'vessel_voyage_id', 'owner', 'load_port', 'location', 'liable_party_id', 'vendor_id', 'status', 'trans_date', 'depot', 'depot_open', 'size', 'type', 'activity_date', 'loc_status', 'vessel_date', 'kind', 'estimate_ref', 'work_order_ref', 'currency', 'ex_rate', 'approved_status', 'total_cost_fc', 'total_cost_lc', 'remarks', 'approved_by', 'approved_on', 'approved'])]
class MaintenanceRepairEntry extends Model
{
    protected function casts(): array
    {
        return [
            'trans_date' => 'datetime',
            'activity_date' => 'datetime',
            'vessel_date' => 'datetime',
            'depot_open' => 'boolean',
            'approved' => 'boolean',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function agent2(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id_2');
    }

    public function vesselVoyage(): BelongsTo
    {
        return $this->belongsTo(VesselVoyage::class);
    }

    public function liableParty(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'liable_party_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'vendor_id');
    }
}
