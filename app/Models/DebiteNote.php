<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['doc_no', 'invoice_id', 'settlement_type_id', 'payment_agent_id', 'currency_id', 'amount', 'supplier_id', 'location_id', 'sub_company_id', 'container_purchase_detail_id', 'currency_exchange_rate', 'currency_code', 'total_amount'])]
final class DebiteNote extends Model
{
    use HasFactory;

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ContainerPurchaseInvoice::class, 'invoice_id');
    }

    public function settlementType(): BelongsTo
    {
        return $this->belongsTo(SettlementType::class);
    }

    public function paymentAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Pol::class);
    }

    public function subCompany(): BelongsTo
    {
        return $this->belongsTo(SubCompany::class);
    }

    public function containerPurchase(): BelongsTo
    {
        return $this->belongsTo(ContainerPurchase::class, 'container_purchase_detail_id');
    }
}
