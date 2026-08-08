<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['transaction_date', 'transaction_number', 'status', 'invoice_date', 'invoice_number', 'period_from', 'period_to', 'vendor', 'port', 'payment_center', 'settlement_type', 'sub_company', 'attachments', 'total', 'vat', 'net_amount', 'approved_by', 'approved_on'])]
class PurchaseInvoice extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'invoice_date' => 'date',
            'period_from' => 'date',
            'period_to' => 'date',
            'approved_on' => 'date',
        ];
    }
}
