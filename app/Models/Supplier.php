<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'email', 'contact', 'address', 'location_id'])]
final class Supplier extends Model
{
    use HasFactory;

    public function location(): BelongsTo
    {
        return $this->belongsTo(Pol::class);
    }
}
