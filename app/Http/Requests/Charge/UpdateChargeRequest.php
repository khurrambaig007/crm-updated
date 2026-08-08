<?php

namespace App\Http\Requests\Charge;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChargeRequest extends FormRequest
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
