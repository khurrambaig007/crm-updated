<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgentRequest extends FormRequest
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
            'pol_id' => ['nullable', 'exists:pols,id'],
            'container_size_id' => ['nullable', 'exists:container_sizes,id'],
            'amount' => ['required', 'numeric'],
        ];
    }
}
