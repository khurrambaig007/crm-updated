<?php

namespace App\Http\Requests\ContainerPurchase;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContainerPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'trans_no' => ['required', 'string', 'max:191'],
            'date' => ['nullable', 'date'],
            'normal_purchase' => ['nullable', 'string', 'in:normal,lease,exchange'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'port_id' => ['nullable', 'exists:pols,id'],
            'handling_id' => ['nullable', 'exists:agents,id'],
            'expected_delivery' => ['nullable', 'date'],
            'release_no' => ['nullable', 'string', 'max:191'],
            'currency' => ['nullable', 'string', 'max:191'],
            'currency_code' => ['nullable', 'string', 'max:10'],
            'rate' => ['nullable', 'numeric'],
            'principal' => ['nullable', 'string', 'max:191'],
        ];
    }
}
