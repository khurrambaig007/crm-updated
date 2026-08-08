<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['size'])]
class ContainerSize extends Model
{
    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class, 'agent_container_sizes');
    }

    public function pols(): HasMany
    {
        return $this->hasMany(Pol::class);
    }
}
