<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['sales_invoice_id', 'description', 'container_number', 'amount'])]
final class SalesInvoiceDetail extends Model
{
    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class);
    }
}
