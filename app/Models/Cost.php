<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['pol_id', 'pod_id', 'container_type_id', 'feeder_id', 'container_size_id', 'slot_term_id', 'pod_agent_id', 'pol_agent_id', 'slot', 'dthc', 'wrr', 'ts_thc', 'ts_commission', 'of', 'pod_rebate', 'free_days', 'total_cost', 'total_collection', 'net_shipping'])]
class Cost extends Model
{
    public function pol(): BelongsTo
    {
        return $this->belongsTo(Pol::class);
    }

    public function pod(): BelongsTo
    {
        return $this->belongsTo(Pod::class);
    }

    public function containerType(): BelongsTo
    {
        return $this->belongsTo(ContainerType::class);
    }

    public function feeder(): BelongsTo
    {
        return $this->belongsTo(Feeder::class);
    }

    public function containerSize(): BelongsTo
    {
        return $this->belongsTo(ContainerSize::class);
    }

    public function slotTerm(): BelongsTo
    {
        return $this->belongsTo(SlotTerm::class);
    }

    public function podAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'pod_agent_id');
    }

    public function polAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'pol_agent_id');
    }

    public function labels(): HasMany
    {
        return $this->hasMany(Label::class);
    }

    public function labelCollections(): HasMany
    {
        return $this->hasMany(LabelCollection::class);
    }
}
