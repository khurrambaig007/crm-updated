<?php

namespace App\Http\Requests\BlInfo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingInfoRequest extends FormRequest
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
        return [
            'booking_info_pol' => ['nullable', 'integer', 'exists:pols,id'],
            'booking_info_cntr_owner' => ['nullable', 'integer', 'exists:carriers,id'],
            'booking_info_reference' => ['nullable', 'string', 'max:191'],
            'booking_info_pofd' => ['nullable', 'integer', 'exists:pods,id'],
            'booking_info_agent_pofd' => ['nullable', 'integer', 'exists:agents,id'],
            'booking_info_pot_1' => ['nullable', 'integer', 'exists:pols,id'],
            'booking_info_agent_1' => ['nullable', 'integer', 'exists:agents,id'],
            'booking_info_pot_2' => ['nullable', 'integer', 'exists:pols,id'],
            'booking_info_agent_2' => ['nullable', 'integer', 'exists:agents,id'],
            'booking_info_shipper_bp' => ['nullable', 'integer', 'exists:shipper_bps,id'],
            'booking_info_consignee' => ['nullable', 'integer', 'exists:p_a_s,id'],
        ];
    }
}
