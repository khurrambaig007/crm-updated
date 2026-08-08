<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['trans_id', 'agent', 'container_no', 'ca_doc_no', 'activity', 'vessel_voyage', 'owner', 'load_port', 'location', 'liable_party', 'vendor', 'status', 'trans_date', 'depot', 'depot_open', 'size', 'type', 'activity_date', 'loc_status', 'vessel_date', 'kind', 'estimate_ref', 'work_order_ref', 'currency', 'ex_rate', 'approved_status', 'total_cost_fc', 'total_cost_lc', 'remarks', 'approved_by', 'approved_on', 'approved'])]
class MaintenanceRepairEntry extends Model
{
    protected function casts(): array
    {
        return [
            'trans_date' => 'datetime',
            'activity_date' => 'datetime',
            'vessel_date' => 'datetime',
            'depot_open' => 'boolean',
            'approved' => 'boolean',
        ];
    }
}
