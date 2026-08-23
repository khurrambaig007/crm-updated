<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['container_purchase_detail_id', 'container_number', 'container_size', 'container_type', 'container_kind', 'm_f_year', 'rate', 'remarks', 'original_container_number'])]
final class ContainerPurchaseRelease extends Model
{
    use HasFactory;

    public function containerPurchase(): BelongsTo
    {
        return $this->belongsTo(ContainerPurchase::class, 'container_purchase_detail_id');
    }

    public function containerSize(): BelongsTo
    {
        return $this->belongsTo(ContainerSize::class, 'container_size');
    }

    public function containerType(): BelongsTo
    {
        return $this->belongsTo(ContainerType::class, 'container_type');
    }

    public function containerKind(): BelongsTo
    {
        return $this->belongsTo(ContainerKind::class, 'container_kind');
    }
}
