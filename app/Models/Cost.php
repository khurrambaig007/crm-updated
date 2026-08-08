<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pol_id', 'pod_id', 'feeder_id', 'agent_id', 'term', 'total', 'slot', 'pol_agent', 'dthc', 'wrr', 'lss', 'dg', 'pod_agent', 'of', 'lthc', 'pod_r', 'free_days', 'total_collection', 'net_total', 'container_type_id', 'container_size_id', 'slot_term_id', 'pol_commission_id', 'pod_commission_id'])]
class Cost extends Model
{
    public function pol(): BelongsTo
    {
        return $this->belongsTo(Pol::class);
    }

    public function pod(): BelongsTo
    {
        return $this->belongsTo(Pol::class, 'pod_id');
    }

    public function feeder(): BelongsTo
    {
        return $this->belongsTo(Feeder::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function containerType(): BelongsTo
    {
        return $this->belongsTo(ContainerType::class);
    }

    public function containerSize(): BelongsTo
    {
        return $this->belongsTo(ContainerSize::class);
    }

    public function slotTerm(): BelongsTo
    {
        return $this->belongsTo(SlotTerm::class);
    }

    public function polCommission(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'pol_commission_id');
    }

    public function podCommission(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'pod_commission_id');
    }
}
