<?php

namespace App\Http\Requests\SubCompany;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact' => ['nullable', 'string', 'max:20'],
        ];
    }
}
