<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['invoice_id', 'container_purchase_detail_id', 'transaction_date'])]
final class PoCancel extends Model
{
    use HasFactory;

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ContainerPurchaseInvoice::class, 'invoice_id');
    }

    public function containerPurchase(): BelongsTo
    {
        return $this->belongsTo(ContainerPurchase::class, 'container_purchase_detail_id');
    }

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
        ];
    }
}
