<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['exchange_rate_date', 'exchange_rate'])]
final class Currency extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'exchange_rate_date' => 'date',
        ];
    }
}
