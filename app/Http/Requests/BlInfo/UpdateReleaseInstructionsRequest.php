<?php

namespace App\Http\Requests\BlInfo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReleaseInstructionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $releaseStatuses = implode(',', array_keys(config('dropdowns.bl_info.release_status')));
        $releaseTypes = implode(',', array_keys(config('dropdowns.bl_info.release_type')));

        return [
            'release_instruction_date' => ['required', 'date'],
            'release_instruction_status' => ['nullable', 'integer', 'in:'.$releaseStatuses],
            'release_instruction_type' => ['nullable', 'integer', 'in:'.$releaseTypes],
            'release_instruction_description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
