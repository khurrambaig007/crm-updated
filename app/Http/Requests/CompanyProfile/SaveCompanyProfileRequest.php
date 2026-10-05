<?php

namespace App\Http\Requests\CompanyProfile;

use App\Models\CompanyProfile;
use Illuminate\Foundation\Http\FormRequest;

abstract class SaveCompanyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Drop blank rows before validating.
     *
     * The repeatable inputs always submit an extra row: a seeded first row on a new
     * profile, or any unused "Add Email" / "Add Field" row the user opened and left
     * alone. Validating those would fail the save with "The emails.1 field is
     * required" for an input the user has no reason to fill in, so they are removed
     * here. That makes `required|min:1` on the array mean "at least one real email".
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('emails')) {
            $this->merge([
                'emails' => CompanyProfile::normalizeEmails($this->input('emails', [])),
            ]);
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'subtitle' => ['nullable', 'string', 'max:191'],
            'logo' => $this->logoRules(),
            'website' => ['nullable', 'string', 'max:191'],
            'number' => ['nullable', 'string', 'max:191'],
            'emails' => ['required', 'array', 'min:1'],
            'emails.*' => ['required', 'email', 'max:191'],
            'pic_name' => ['nullable', 'string', 'max:191'],
            'pic_email' => ['nullable', 'email', 'max:191'],
            'pic_number' => ['nullable', 'string', 'max:191'],
            'custom_fields' => ['nullable', 'array'],
            'custom_fields.*.key' => ['nullable', 'string', 'max:191'],
            'custom_fields.*.value' => ['nullable', 'string', 'max:191'],
            'message' => ['nullable', 'string'],
            'billing_address' => ['nullable', 'string', 'max:3000'],
            'invoice_payment_instructions' => ['nullable', 'string', 'max:5000'],
            'remove_logo' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<int, mixed>
     */
    abstract protected function logoRules(): array;
}
