<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'code', 'email', 'address', 'phone_No', 'website', 'agent_id', 'type', 'line_type', 'agent_name'])]
class PAS extends Model
{
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function agentName(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_name');
    }
}
