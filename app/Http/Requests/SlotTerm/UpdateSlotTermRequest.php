<?php

namespace App\Http\Requests\SlotTerm;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSlotTermRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'term' => ['required', 'string', 'max:255'],
        ];
    }
}
