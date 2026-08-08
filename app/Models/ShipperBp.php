<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['code', 'agent_id', 'name', 'port_id', 'type', 'tax_id', 'phone_no', 'web', 'address', 'email', 'fax', 'shipper', 'ca', 'consignee'])]
class ShipperBp extends Model
{
    protected function casts(): array
    {
        return [
            'shipper' => 'boolean',
            'ca' => 'boolean',
            'consignee' => 'boolean',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function port(): BelongsTo
    {
        return $this->belongsTo(Pol::class);
    }
}
