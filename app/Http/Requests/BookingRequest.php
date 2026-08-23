<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_no' => 'required|string|max:191',
            'approval_no' => 'required|string|max:191',
            'reference_no' => 'required|string|max:191',
            'booking_date' => 'required|date',
            'sailing_date' => 'required|date',
            'carrier' => 'nullable|integer',
            'cntr_owner' => 'nullable|integer',
            'commodity' => 'nullable|integer',
            'non_dg' => 'nullable|integer',
            'vessel_voyage' => 'nullable|integer',
            'pol' => 'nullable|integer',
            'pofd' => 'nullable|integer',
            'pot_1' => 'nullable|integer',
            'pot_2' => 'nullable|integer',
            'shipper_bp' => 'nullable|integer',
            'agent_pol' => 'nullable|integer',
            'agent_pofd' => 'nullable|integer',
            'agent_1' => 'nullable|integer',
            'agent_2' => 'nullable|integer',
            'freight_type' => 'nullable|integer',
            'consignee' => 'nullable|integer',
            'thru_bl' => 'boolean',
        ];
    }
}
