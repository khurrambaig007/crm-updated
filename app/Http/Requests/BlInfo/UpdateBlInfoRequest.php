<?php

namespace App\Http\Requests\BlInfo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $siStatuses = implode(',', array_keys(config('dropdowns.bl_info.booking_si_status')));

        return [
            'bl_info_date' => ['required', 'date'],
            'bl_info_agent' => ['nullable', 'integer', 'exists:agents,id'],
            'bl_info_accounting_date' => ['required', 'date', 'after_or_equal:bl_info_date'],
            'bl_info_carrier_mbl_no' => ['nullable', 'string', 'max:191'],
            'bl_info_vessel_voyage_1' => ['nullable', 'integer', 'exists:vessel_voyages,id'],
            'bl_info_booking_si_status' => ['nullable', 'integer', 'in:'.$siStatuses],
            'bl_info_transshipment' => ['boolean'],
        ];
    }
}
