<?php

namespace App\Http\Requests\FreightType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFreightTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
        ];
    }
}
