<?php

namespace App\Http\Requests\ContainerType;

use Illuminate\Foundation\Http\FormRequest;

class StoreContainerTypeRequest extends FormRequest
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
