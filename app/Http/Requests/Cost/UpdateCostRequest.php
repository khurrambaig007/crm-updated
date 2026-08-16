<?php

namespace App\Http\Requests\Cost;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pol_id' => ['nullable', 'exists:pols,id'],
            'pod_id' => ['nullable', 'exists:pods,id'],
            'container_type_id' => ['nullable', 'exists:container_types,id'],
            'feeder_id' => ['nullable', 'exists:feeders,id'],
            'container_size_id' => ['nullable', 'exists:container_sizes,id'],
            'slot_term_id' => ['nullable', 'exists:slot_terms,id'],
            'pod_agent_id' => ['nullable', 'exists:agents,id'],
            'pol_agent_id' => ['nullable', 'exists:agents,id'],
            'slot' => ['nullable', 'string', 'max:191'],
            'dthc' => ['nullable', 'string', 'max:191'],
            'wrr' => ['nullable', 'string', 'max:191'],
            'ts_thc' => ['nullable', 'string', 'max:191'],
            'ts_commission' => ['nullable', 'string', 'max:191'],
            'of' => ['nullable', 'string', 'max:191'],
            'pod_rebate' => ['nullable', 'string', 'max:191'],
            'free_days' => ['nullable', 'string', 'max:191'],
            'total_cost' => ['nullable', 'string', 'max:191'],
            'total_collection' => ['nullable', 'string', 'max:191'],
            'net_shipping' => ['nullable', 'string', 'max:191'],
            'labels' => ['nullable', 'array'],
            'labels.*.key' => ['nullable', 'string', 'max:191'],
            'labels.*.value' => ['nullable', 'string', 'max:191'],
            'label_collections' => ['nullable', 'array'],
            'label_collections.*.key' => ['nullable', 'string', 'max:191'],
            'label_collections.*.value' => ['nullable', 'string', 'max:191'],
        ];
    }
}
