<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['containe_no', 'size_type', 'principle', 'bl_number', 'booking_number', 'status', 'cargo_type', 'one_door_open', 'last_activity', 'system_remarks', 'vessel_ts1', 'voyage_ts1', 'sailing_date_ts1', 'vessel_ts2', 'voyage_ts2', 'sailing_date_ts2', 'vessel_ts3', 'voyage_ts3', 'sailing_date_ts3', 'container_activity_id'])]
class ContainerActivityDetail extends Model
{
    protected function casts(): array
    {
        return [
            'one_door_open' => 'boolean',
        ];
    }

    public function containerActivity(): BelongsTo
    {
        return $this->belongsTo(ContainerActivity::class);
    }
}
