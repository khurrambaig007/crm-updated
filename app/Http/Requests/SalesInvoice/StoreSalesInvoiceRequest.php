<?php

namespace App\Http\Requests\SalesInvoice;

use App\Services\ExchangeRateService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalesInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('details') || ! is_array($this->input('details'))) {
            return;
        }

        $details = array_filter($this->input('details'), function (mixed $detail): bool {
            return is_array($detail)
                && (filled($detail['description'] ?? null)
                    || filled($detail['container_number'] ?? null)
                    || filled($detail['amount'] ?? null));
        });

        $this->merge(['details' => array_values($details)]);
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'currency_code' => ['required', 'string', 'max:10', Rule::in(ExchangeRateService::availableCodes())],
            'status' => ['required', 'string', Rule::in(array_keys(config('dropdowns.sales_invoices.status', [])))],
            'party_id' => ['required', 'integer', 'exists:p_a_s,id'],
            'bank_account_id' => ['nullable', 'integer', 'exists:bank_accounts,id'],
            'invoice_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'our_reference' => ['nullable', 'string', 'max:191'],
            'customer_contact' => ['nullable', 'string', 'max:191'],
            'remarks' => ['nullable', 'string', 'max:3000'],
            'vat_rate' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:100'],
            'details' => ['required', 'array', 'min:1'],
            'details.*.description' => ['required', 'string', 'max:3000'],
            'details.*.container_number' => ['nullable', 'string', 'max:191'],
            'details.*.amount' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }
}
