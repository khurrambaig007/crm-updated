<?php

namespace App\Http\Requests\SettlementType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettlementTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
