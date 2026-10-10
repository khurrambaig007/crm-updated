<?php

declare(strict_types=1);

namespace App\Http\Requests\BlInfo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLockInfoRequest extends FormRequest
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
        return [
            'lock_info_pol_locked_to' => ['nullable', 'integer', 'exists:pols,id'],
            'lock_info_pol_locked_on' => ['nullable', 'date'],
            'lock_info_pot_1_locked_to' => ['nullable', 'integer', 'exists:pols,id'],
            'lock_info_pot_1_locked_on' => ['nullable', 'date'],
            'lock_info_pot_2_locked_to' => ['nullable', 'integer', 'exists:pols,id'],
            'lock_info_pot_2_locked_on' => ['nullable', 'date'],
            'lock_info_pofd_locked_to' => ['nullable', 'integer', 'exists:p_o_d_s,id'],
            'lock_info_pofd_locked_on' => ['nullable', 'date'],
            'lock_info_locked_detail' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
