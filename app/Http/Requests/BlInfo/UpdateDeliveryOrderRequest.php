<?php

namespace App\Http\Requests\BlInfo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'delivery_order_doc' => ['nullable', 'string', 'max:191'],
            'delivery_order_date' => ['required', 'date'],
            'delivery_order_validity_date' => ['nullable', 'date', 'after_or_equal:delivery_order_date'],
            'delivery_order_agent' => ['nullable', 'integer', 'exists:agents,id'],
            'delivery_order_vessel_voyage' => ['nullable', 'integer', 'exists:vessel_voyages,id'],
            'delivery_order_deliver_to' => ['nullable', 'string', 'max:500'],
            'delivery_order_remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
