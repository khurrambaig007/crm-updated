<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pol_id', 'container_size_id'])]
class PortContainerSize extends Model
{
    public function pol(): BelongsTo
    {
        return $this->belongsTo(Pol::class);
    }

    public function containerSize(): BelongsTo
    {
        return $this->belongsTo(ContainerSize::class);
    }
}
