<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['blinfo_container_info', 'blinfo_bl_detail', 'blinfo_refnos', 'booking_id'])]
class BlContainerInfo extends Model
{
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
