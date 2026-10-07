<?php

namespace App\Http\Requests\BankAccount;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * JSON-only variant of the bank-account rules for the quick-create endpoint on
 * the sales invoice form. Validation runs before the controller method, so the
 * JSON 422 must be raised here — on non-api routes a plain ValidationException
 * becomes a 302 redirect (see .ai/rules/controllers.md).
 */
class QuickCreateBankAccountRequest extends SaveBankAccountRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed.',
            'errors' => $validator->errors()->toArray(),
        ], 422));
    }
}
