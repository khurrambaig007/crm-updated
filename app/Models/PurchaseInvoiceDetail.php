<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['purchase_invoice_id', 'charges', 'type', 'm_r_number', 'bl_number', 'container_number', 'size', 'size_type', 'amount', 'currency', 'exchange_rate', 'amount_dollar', 'vat_percentage', 'vat_amount', 'vat_amount_dollar', 'remarks'])]
final class PurchaseInvoiceDetail extends Model
{
    use HasFactory, SoftDeletes;

    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }
}
