<?php

namespace App\Http\Requests\ContainerReleaseOrder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContainerReleaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['nullable', 'integer', 'exists:bookings,id'],
            'booking_no' => ['required', 'string', 'max:191'],
            'reference_no' => ['required', 'string', 'max:191'],
            'booking_date' => ['required', 'date'],
            'cntr_owner' => ['required', 'integer', 'in:'.implode(',', array_keys(config('dropdowns.container_release_orders.cntr_owner')))],
            'commodity_id' => ['required', 'integer', 'exists:commodities,id'],
            'dg_status' => ['required', 'integer', 'in:'.implode(',', array_keys(config('dropdowns.container_release_orders.dg_status')))],
            'pol_id' => ['required', 'integer', 'exists:pols,id'],
            'pofd_id' => ['required', 'integer', 'exists:pods,id'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
