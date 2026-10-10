<?php

declare(strict_types=1);

namespace App\Http\Requests\BlInfo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * Row-level validation for the Container Info grid.
 *
 * These endpoints are AJAX-only, so a failed validation must surface as a real
 * JSON 422 rather than the 302 a bare ValidationException produces on non-api
 * routes — the grid's error handler reads responseJSON.errors.
 */
class ContainerInfoRequest extends FormRequest
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
        $cntrOwners = Rule::in(array_keys(config('dropdowns.bookings.cntr_owner')));
        $slot = Rule::exists('slot_terms', 'id');

        return [
            'container_info_cntr_owner' => ['nullable', 'integer', $cntrOwners],

            'container_info_size' => ['nullable', 'string', 'max:191'],
            'container_info_container_no' => ['nullable', 'string', 'max:191'],
            'container_info_seal_no' => ['nullable', 'string', 'max:191'],
            'container_info_seal2_no' => ['nullable', 'string', 'max:191'],
            'container_info_load_type' => ['nullable', 'string', 'max:191'],

            // Free text on purpose: figures are pasted in as written and must
            // survive round-tripping without being coerced or rejected.
            'container_info_gross_weight' => ['nullable', 'string', 'max:191'],
            'container_info_net_weight' => ['nullable', 'string', 'max:191'],
            'container_info_cbm' => ['nullable', 'string', 'max:191'],
            'container_info_packages' => ['nullable', 'string', 'max:191'],
            'container_info_unit' => ['nullable', 'string', 'max:16'],
            'container_info_temperature' => ['nullable', 'string', 'max:191'],

            'container_info_slot_operator' => ['nullable', 'integer', $slot],
            'container_info_slot_operator_ts1' => ['nullable', 'integer', $slot],
            'container_info_slot_operator_ts2' => ['nullable', 'integer', $slot],

            'container_info_imo_class' => ['nullable', 'string', 'max:191'],
            'container_info_un_number' => ['nullable', 'string', 'max:191'],
            'container_info_remarks' => ['nullable', 'string', 'max:191'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'container_info_cntr_owner' => 'container owner',
            'container_info_size' => 'size/type',
            'container_info_container_no' => 'container number',
            'container_info_seal_no' => 'seal number',
            'container_info_seal2_no' => 'second seal number',
            'container_info_load_type' => 'load type',
            'container_info_gross_weight' => 'gross weight',
            'container_info_net_weight' => 'net weight',
            'container_info_cbm' => 'CBM',
            'container_info_packages' => 'packages',
            'container_info_unit' => 'unit',
            'container_info_temperature' => 'temperature',
            'container_info_slot_operator' => 'slot operator',
            'container_info_slot_operator_ts1' => 'slot operator (TS1)',
            'container_info_slot_operator_ts2' => 'slot operator (TS2)',
            'container_info_imo_class' => 'IMO class',
            'container_info_un_number' => 'UN number',
            'container_info_remarks' => 'remarks',
        ];
    }

    /**
     * @param  array<string, string>  $messages
     */
    protected function failedValidation(ValidationValidator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'The container information is invalid.',
            'errors' => $validator->errors()->toArray(),
        ], 422));
    }
}
