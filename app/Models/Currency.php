<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['exchange_rate_date', 'exchange_rate'])]
class Currency extends Model
{
    protected function casts(): array
    {
        return [
            'exchange_rate_date' => 'date',
        ];
    }
}
