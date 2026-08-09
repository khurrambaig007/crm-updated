<?php

namespace App\Http\Requests\PurchaseInvoice;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_date' => ['required', 'date'],
            'transaction_number' => ['required', 'string', 'max:191'],
            'status' => ['nullable', 'integer', 'in:0,1,2'],
            'purchase_invoice_date' => ['required', 'date'],
            'invoice_number' => ['required', 'string', 'max:191'],
            'period_from' => ['required', 'date'],
            'period_to' => ['required', 'date'],
            'vendor_id' => ['nullable', 'exists:suppliers,id'],
            'port_id' => ['nullable', 'exists:pols,id'],
            'payment_center' => ['nullable', 'string'],
            'settlement_type_id' => ['nullable', 'exists:settlement_types,id'],
            'sub_company_id' => ['nullable', 'exists:sub_companies,id'],
        ];
    }
}
