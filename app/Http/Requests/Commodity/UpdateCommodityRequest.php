<?php

namespace App\Http\Requests\Commodity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommodityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'commodity_number' => ['required', 'string', 'max:255'],
        ];
    }
}
