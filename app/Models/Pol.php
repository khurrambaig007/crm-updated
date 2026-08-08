<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['city', 'country', 'port_code', 'rebate', 'container_size_id'])]
class Pol extends Model
{
    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    public function containerSize(): BelongsTo
    {
        return $this->belongsTo(ContainerSize::class);
    }
}
