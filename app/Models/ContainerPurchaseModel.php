<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['auto', 'container_size_id', 'container_type_id', 'container_kind_id', 'quantity', 'price', 'total', 'rate', 'container_purchase_detail_id'])]
class ContainerPurchaseModel extends Model
{
    public function containerSize(): BelongsTo
    {
        return $this->belongsTo(ContainerSize::class);
    }

    public function containerType(): BelongsTo
    {
        return $this->belongsTo(ContainerType::class);
    }

    public function containerKind(): BelongsTo
    {
        return $this->belongsTo(ContainerKind::class);
    }

    public function containerPurchase(): BelongsTo
    {
        return $this->belongsTo(ContainerPurchase::class, 'container_purchase_detail_id');
    }
}
