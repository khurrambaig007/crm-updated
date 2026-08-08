<?php

namespace App\Http\Requests\Party;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'phone_No' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'string', 'max:255'],
            'agent_id' => ['required', 'exists:agents,id'],
            'type' => ['required', 'in:Customer,Vendor,Both'],
            'line_type' => ['required', 'in:Direct Customer,Traders'],
        ];
    }
}
