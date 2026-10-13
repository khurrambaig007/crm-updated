<?php

declare(strict_types=1);

namespace App\Http\Requests\BlInfo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Ref NO's screen.
 *
 * Every field is optional: a booking can hold an export number long before it
 * holds a letter-of-credit number, so requiring any of them would block
 * saving the screen until the paperwork is complete.
 */
class UpdateRefnosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'refno_invoice_no' => ['nullable', 'string', 'max:191'],
            'refno_invoice_date' => ['nullable', 'date'],
            'refno_export_no' => ['nullable', 'string', 'max:191'],
            'refno_export_date' => ['nullable', 'date'],
            'refno_contact_no' => ['nullable', 'string', 'max:191'],
            'refno_contact_date' => ['nullable', 'date'],
            'refno_lc_no' => ['nullable', 'string', 'max:191'],
            'refno_lc_date' => ['nullable', 'date'],
            'refno_shipper_ref' => ['nullable', 'string', 'max:191'],
            'refno_client_ref' => ['nullable', 'string', 'max:191'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'refno_invoice_no' => 'invoice number',
            'refno_invoice_date' => 'invoice date',
            'refno_export_no' => 'export number',
            'refno_export_date' => 'export date',
            'refno_contact_no' => 'contact number',
            'refno_contact_date' => 'contact date',
            'refno_lc_no' => 'L/C number',
            'refno_lc_date' => 'L/C date',
            'refno_shipper_ref' => 'shipper reference',
            'refno_client_ref' => 'client reference',
        ];
    }
}
