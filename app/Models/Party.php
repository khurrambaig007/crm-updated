<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'code', 'email', 'address', 'phone_No', 'website', 'agent_id', 'type', 'line_type'])]
class Party extends Model
{
    protected $table = 'p_a_s';

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
