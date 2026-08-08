<?php

namespace App\Http\Requests\ShipperBp;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipperBpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'agent_id' => ['required', 'exists:agents,id'],
            'name' => ['required', 'string', 'max:255'],
            'port_id' => ['required', 'exists:pols,id'],
            'type' => ['nullable', 'in:Direct Party,Forwarder'],
            'tax_id' => ['nullable', 'string', 'max:255'],
            'phone_no' => ['nullable', 'string', 'max:20'],
            'web' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'fax' => ['nullable', 'string', 'max:255'],
            'shipper' => ['nullable', 'boolean'],
            'ca' => ['nullable', 'boolean'],
            'consignee' => ['nullable', 'boolean'],
        ];
    }
}
