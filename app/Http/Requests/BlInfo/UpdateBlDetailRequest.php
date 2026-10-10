<?php

declare(strict_types=1);

namespace App\Http\Requests\BlInfo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $units = Rule::in(array_keys(config('dropdowns.bl_info.units')));
        $freightTypes = Rule::in(array_keys(config('dropdowns.bookings.freight_type')));

        return [
            'bl_detail_shipper' => ['required', 'integer', 'exists:shipper_bps,id'],
            'bl_detail_consignee' => ['required', 'integer', 'exists:p_a_s,id'],
            'bl_detail_notify_party_1' => ['nullable', 'string', 'max:191'],
            'bl_detail_notify_party_2' => ['nullable', 'string', 'max:191'],

            'bl_detail_pol' => ['required', 'integer', 'exists:pols,id'],
            'bl_detail_vessel_voyage' => ['nullable', 'integer', 'exists:vessel_voyages,id'],
            'bl_detail_pofd' => ['required', 'integer', 'exists:pods,id'],
            'bl_detail_place_of_delivery' => ['nullable', 'string', 'max:191'],

            'bl_detail_original_bls' => ['nullable', 'string', 'max:191'],
            'bl_detail_units' => ['nullable', $units],
            'bl_detail_freight_type' => ['nullable', $freightTypes],
            'bl_detail_delivery_agent' => ['nullable', 'integer', 'exists:agents,id'],

            'bl_detail_marks_and_numbers' => ['nullable', 'string', 'max:2000'],
            'bl_detail_no_of_packages' => ['nullable', 'string', 'max:1000'],
            'bl_detail_description_of_goods' => ['nullable', 'string', 'max:2000'],
            'bl_detail_gross_weight' => ['nullable', 'string', 'max:1000'],
            'bl_detail_measurement' => ['nullable', 'string', 'max:1000'],

            'bl_detail_on_board_date' => ['nullable', 'date'],
            'bl_detail_place_of_issue' => ['nullable', 'string', 'max:191'],
            'bl_detail_date_of_issue' => ['required', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'bl_detail_shipper' => 'shipper',
            'bl_detail_consignee' => 'consignee',
            'bl_detail_pol' => 'port of loading',
            'bl_detail_pofd' => 'port of discharge',
            'bl_detail_original_bls' => "number of original B/L's",
            'bl_detail_units' => 'units',
            'bl_detail_freight_type' => 'freight type',
            'bl_detail_delivery_agent' => 'delivery agent',
            'bl_detail_marks_and_numbers' => 'marks and container numbers',
            'bl_detail_no_of_packages' => 'number of packages',
            'bl_detail_description_of_goods' => 'description of goods and packages',
            'bl_detail_gross_weight' => 'gross weight',
            'bl_detail_measurement' => 'measurement',
            'bl_detail_on_board_date' => 'on board date',
            'bl_detail_place_of_issue' => 'place of issue',
            'bl_detail_date_of_issue' => 'date of issue',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bl_detail_date_of_issue.required' => 'A bill of lading cannot be issued without a date of issue.',
            'bl_detail_shipper.required' => 'The bill of lading must name a shipper.',
            'bl_detail_consignee.required' => 'The bill of lading must name a consignee.',
            'bl_detail_pol.required' => 'The bill of lading must state a port of loading.',
            'bl_detail_pofd.required' => 'The bill of lading must state a port of discharge.',
        ];
    }
}
