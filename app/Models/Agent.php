<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'code', 'pol_id', 'amount'])]
class Agent extends Model
{
    public function pol(): BelongsTo
    {
        return $this->belongsTo(Pol::class);
    }

    public function containerSizes(): BelongsToMany
    {
        return $this->belongsToMany(ContainerSize::class, 'agent_container_sizes');
    }
}
