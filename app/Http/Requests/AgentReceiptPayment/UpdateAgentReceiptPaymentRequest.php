<?php

namespace App\Http\Requests\AgentReceiptPayment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgentReceiptPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_no' => ['required', 'string', 'max:191'],
            'receipt_date' => ['nullable', 'date'],
            'agent' => ['nullable', 'string'],
            'mode' => ['nullable', 'integer', 'in:1,2'],
            'sub_mode' => ['nullable', 'integer', 'in:1,2,3,4,5'],
            'currency' => ['nullable', 'string', 'max:100'],
            'exchange_rate' => ['nullable', 'numeric'],
            'remarks' => ['nullable', 'string', 'max:1500'],
            'act_mode' => ['nullable', 'integer', 'in:1,2,3'],
            'bank_charges' => ['nullable', 'numeric'],
            'ac_code' => ['nullable', 'string'],
            'cheque_no' => ['nullable', 'string', 'max:191'],
            'cheque_date' => ['nullable', 'date'],
            'gain_loss' => ['nullable', 'numeric'],
            'total_amount' => ['nullable', 'numeric'],
            'total_amount_1' => ['nullable', 'numeric'],
            'approved_by' => ['nullable', 'string', 'max:191'],
            'approved_on' => ['nullable', 'string', 'max:191'],
        ];
    }
}
