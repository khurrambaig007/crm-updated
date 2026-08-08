<?php

namespace App\Http\Requests\ContainerSize;

use Illuminate\Foundation\Http\FormRequest;

class StoreContainerSizeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'size' => ['required', 'string', 'max:255'],
        ];
    }
}
