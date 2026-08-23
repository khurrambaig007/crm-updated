<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'doc_no',
    'activity_date',
    'agent',
    'activity',
    'free_days',
    'bl_number',
    'booking_number',
    'final_destination_code',
    'pod_code',
    'destination_agent',
    'thru_bl',
    'sailing_date',
    'vessel_voyage_id',
    'voyage_number',
    'location',
    'carrier',
    'ts_1_port',
    'ts_1_port_id',
    'ts_1_agent',
    'ts_1_agent_id',
    'ts_2_port',
    'ts_2_port_id',
    'ts_2_agent',
    'ts_2_agent_id',
    'ts_3_port',
    'ts_3_port_id',
    'ts_3_agent',
    'ts_3_agent_id',
    'remarks',
])]
final class ContainerActivity extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'sailing_date' => 'date',
            'free_days' => 'integer',
            'thru_bl' => 'boolean',
        ];
    }

    public function vesselVoyage(): BelongsTo
    {
        return $this->belongsTo(VesselVoyage::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ContainerActivityDetail::class);
    }
}
