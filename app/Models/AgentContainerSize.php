<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['agent_id', 'container_size_id'])]
class AgentContainerSize extends Model
{
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function containerSize(): BelongsTo
    {
        return $this->belongsTo(ContainerSize::class);
    }
}
