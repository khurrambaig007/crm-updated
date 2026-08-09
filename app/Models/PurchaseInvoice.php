<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['transaction_date', 'transaction_number', 'status', 'purchase_invoice_date', 'invoice_number', 'period_from', 'period_to', 'vendor_id', 'port_id', 'payment_center', 'settlement_type_id', 'sub_company_id', 'attachments', 'total', 'vat', 'net_amount', 'approved_by', 'approved_on'])]
class PurchaseInvoice extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'purchase_invoice_date' => 'date',
            'period_from' => 'date',
            'period_to' => 'date',
            'approved_on' => 'date',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'vendor_id');
    }

    public function port(): BelongsTo
    {
        return $this->belongsTo(Pol::class, 'port_id');
    }

    public function settlementType(): BelongsTo
    {
        return $this->belongsTo(SettlementType::class);
    }

    public function subCompany(): BelongsTo
    {
        return $this->belongsTo(SubCompany::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceDetail::class);
    }
}
