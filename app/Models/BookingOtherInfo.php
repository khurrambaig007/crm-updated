<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['special_req', 'free_days_pol', 'detention_free_pofd', 'detention_tariff', 'detention_currency', 'message', 'booking_id'])]
class BookingOtherInfo extends Model
{
    protected function casts(): array
    {
        return [
            'detention_tariff' => 'boolean',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
