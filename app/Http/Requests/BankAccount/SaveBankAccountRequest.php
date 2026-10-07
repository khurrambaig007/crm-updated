<?php

namespace App\Http\Requests\BankAccount;

use Illuminate\Foundation\Http\FormRequest;

abstract class SaveBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Blank custom-field rows do not need prepareForValidation() stripping:
     * custom_fields carries no array-level required/min:1 rule, and unused rows
     * are already discarded server-side by BankAccount::normalizeCustomFields().
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'bank' => ['nullable', 'string', 'max:191'],
            'beneficiary_name' => ['required', 'string', 'max:191'],
            'bank_name' => ['nullable', 'string', 'max:191'],
            'account' => ['required', 'string', 'max:191'],
            'iban' => ['nullable', 'string', 'max:191'],
            'swift' => ['nullable', 'string', 'max:191'],
            'address' => ['nullable', 'string'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*.key' => ['nullable', 'string', 'max:191'],
            'custom_fields.*.value' => ['nullable', 'string', 'max:191'],
        ];
    }
}
