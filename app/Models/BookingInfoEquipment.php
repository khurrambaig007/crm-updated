<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['size', 'type', 'quantity', 'gross_weight', 'packages', 'unit', 'cargo_volumn', 'booking_id', 'approval_status'])]
class BookingInfoEquipment extends Model
{
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function containerSize(): BelongsTo
    {
        return $this->belongsTo(ContainerSize::class, 'size');
    }

    public function containerType(): BelongsTo
    {
        return $this->belongsTo(ContainerType::class, 'type');
    }
}
