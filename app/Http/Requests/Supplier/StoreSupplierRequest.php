<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
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
            'location_id' => ['nullable', 'exists:pols,id'],
            'address' => ['required', 'string', 'max:3000'],
        ];
    }
}
