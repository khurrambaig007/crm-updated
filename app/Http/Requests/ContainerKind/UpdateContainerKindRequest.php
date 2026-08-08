<?php

namespace App\Http\Requests\ContainerKind;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContainerKindRequest extends FormRequest
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
