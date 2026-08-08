<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['train_no', 'date', 'normal_purchase', 'supplier_id', 'port_id', 'handling_id', 'expected_delivery', 'po_no', 'release_no', 'currency', 'rate', 'principal', 'trans_no', 'currency_code'])]
class ContainerPurchase extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'expected_delivery' => 'date',
        ];
    }
}
