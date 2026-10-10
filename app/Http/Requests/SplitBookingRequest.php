<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SplitBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $approvalStatuses = implode(',', array_keys(config('dropdowns.bookings.approval_status')));

        return [
            'equipment' => ['required', 'array', 'min:1'],
            'equipment.*.size' => ['required', 'integer', 'exists:container_sizes,id'],
            'equipment.*.type' => ['required', 'integer', 'exists:container_types,id'],
            'equipment.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'equipment.*.gross_weight' => ['nullable', 'string', 'max:191'],
            'equipment.*.packages' => ['nullable', 'string', 'max:191'],
            'equipment.*.unit' => ['nullable', 'string', Rule::in(array_keys(config('dropdowns.bl_info.units')))],
            'equipment.*.cargo_volumn' => ['nullable', 'string', 'max:191'],
            'equipment.*.approval_status' => ['nullable', 'integer', 'in:'.$approvalStatuses],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'equipment.required' => 'Select the equipment you want to move to the new booking.',
            'equipment.min' => 'Select the equipment you want to move to the new booking.',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function equipmentRows(): array
    {
        return array_values(array_filter(
            (array) $this->validated()['equipment'],
            fn (array $row) => (float) ($row['quantity'] ?? 0) > 0
        ));
    }

    protected function prepareForValidation(): void
    {
        $rows = (array) $this->input('equipment', []);

        $this->merge([
            'equipment' => array_values(array_map(function ($row) {
                $row = (array) $row;

                if (isset($row['quantity']) && $row['quantity'] !== '') {
                    $row['quantity'] = (float) $row['quantity'];
                } else {
                    $row['quantity'] = null;
                }

                // unit is a unit code (KGS/MT/LBS), not a number — only trim it.
                $row['unit'] = isset($row['unit']) ? (trim((string) $row['unit']) ?: null) : null;

                return $row;
            }, $rows)),
        ]);
    }
}
