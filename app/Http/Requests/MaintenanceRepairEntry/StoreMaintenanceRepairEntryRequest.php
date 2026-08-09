<?php

namespace App\Http\Requests\MaintenanceRepairEntry;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRepairEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trans_id' => ['required', 'string', 'max:191'],
            'agent_id' => ['nullable', 'exists:agents,id'],
            'agent_id_2' => ['nullable', 'exists:agents,id'],
            'container_no' => ['required', 'string', 'max:191'],
            'ca_doc_no' => ['required', 'string', 'max:191'],
            'vessel_voyage_id' => ['nullable', 'exists:vessel_voyages,id'],
            'owner' => ['nullable', 'string', 'max:191'],
            'load_port' => ['nullable', 'string', 'max:191'],
            'location' => ['nullable', 'string', 'max:191'],
            'liable_party_id' => ['nullable', 'exists:p_a_s,id'],
            'vendor_id' => ['nullable', 'exists:suppliers,id'],
            'status' => ['nullable', 'string', 'max:191'],
        ];
    }
}
