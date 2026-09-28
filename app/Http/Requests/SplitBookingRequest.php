<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'equipment.*.unit' => ['nullable', 'integer'],
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

                foreach (['quantity', 'unit'] as $numeric) {
                    if (isset($row[$numeric]) && $row[$numeric] !== '') {
                        $row[$numeric] = (float) $row[$numeric];
                    } else {
                        $row[$numeric] = null;
                    }
                }

                return $row;
            }, $rows)),
        ]);
    }
}
