<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['charge_id', 'container_size_id', 'container_type_id', 'quantity', 'mrg', 'cost', 'amount', 'currency', 'ex_rate', 'amount_in_dollar', 'pa_party_tpa_agent', 'booking_id', 'freight_type', 'hide', 'remarks', 'slot_term'])]
final class BookingCost extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'mrg' => 'float',
            'cost' => 'float',
            'amount' => 'float',
            'ex_rate' => 'float',
            'amount_in_dollar' => 'float',
            'hide' => 'boolean',
        ];
    }

    public function charge(): BelongsTo
    {
        return $this->belongsTo(Charge::class);
    }

    public function containerSize(): BelongsTo
    {
        return $this->belongsTo(ContainerSize::class);
    }

    public function containerType(): BelongsTo
    {
        return $this->belongsTo(ContainerType::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function slotTerm(): BelongsTo
    {
        return $this->belongsTo(SlotTerm::class, 'slot_term');
    }
}
