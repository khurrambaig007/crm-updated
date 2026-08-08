<?php

namespace App\Http\Requests\CurrencyExchangeRate;

use App\Services\ExchangeRateService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyExchangeRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return collect(ExchangeRateService::CURRENCIES)
            ->mapWithKeys(fn (string $code) => [$code => ['nullable', 'numeric']])
            ->all();
    }
}
