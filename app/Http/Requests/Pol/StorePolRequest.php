<?php

namespace App\Http\Requests\Pol;

use Illuminate\Foundation\Http\FormRequest;

class StorePolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'port_code' => ['required', 'string', 'max:255'],
            'rebate' => ['nullable', 'string', 'max:255'],
            'container_size_id' => ['required', 'exists:container_sizes,id'],
        ];
    }
}
