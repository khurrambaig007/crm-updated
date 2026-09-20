<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'booking_id',
    'booking_no',
    'reference_no',
    'booking_date',
    'cntr_owner',
    'commodity_id',
    'dg_status',
    'pol_id',
    'pofd_id',
    'notes',
])]
final class ContainerReleaseOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    public function pol(): BelongsTo
    {
        return $this->belongsTo(Pol::class);
    }

    public function pofd(): BelongsTo
    {
        return $this->belongsTo(Pod::class);
    }
}
