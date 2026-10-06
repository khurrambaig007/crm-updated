<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['currency_code', 'invoice_number', 'party_id', 'invoice_date', 'due_date', 'our_reference', 'customer_contact', 'remarks', 'vat_rate', 'subtotal', 'vat_amount', 'total_amount', 'status'])]
final class SalesInvoice extends Model
{
    protected const INVOICE_NUMBER_PREFIX = 'APX';

    protected const INVOICE_NUMBER_PAD = 8;

    protected static function booted(): void
    {
        self::created(function (self $invoice): void {
            $invoice->forceFill([
                'invoice_number' => self::INVOICE_NUMBER_PREFIX.str_pad((string) $invoice->id, self::INVOICE_NUMBER_PAD, '0', STR_PAD_LEFT),
            ])->saveQuietly();
        });
    }

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'vat_rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'currency_code' => 'string',
            'status' => 'string',
        ];
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(SalesInvoiceDetail::class);
    }
}
