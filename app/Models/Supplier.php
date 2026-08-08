<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'email', 'contact', 'address', 'location_id'])]
class Supplier extends Model
{
    public function location(): BelongsTo
    {
        return $this->belongsTo(Pol::class);
    }
}
