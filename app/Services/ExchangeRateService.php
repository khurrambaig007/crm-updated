<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ExchangeRateService
{
    public const BASE_CURRENCY = 'USD';

    public const CURRENCIES = ['PKR', 'INR', 'USD', 'MYR', 'AED', 'SAR', 'CNY'];

    /** @return list<string> */
    public static function availableCodes(): array
    {
        $rates = Currency::query()
            ->orderByDesc('exchange_rate_date')
            ->value('exchange_rate');

        $codes = array_keys((array) json_decode((string) $rates, true) ?: []);

        return $codes === [] ? self::CURRENCIES : array_values($codes);
    }

    public function fetchAndStore(array $currencies = self::CURRENCIES, string $base = self::BASE_CURRENCY): Currency
    {
        $apiKey = config('services.exchange_rate.api_key');

        if (blank($apiKey)) {
            throw new RuntimeException('Exchange rate API key is not configured.');
        }

        $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/latest/{$base}";

        try {
            $response = Http::acceptJson()->get($url);
            $response->throw();
        } catch (ConnectionException|RequestException $e) {
            throw new RuntimeException("Failed to fetch exchange rates: {$e->getMessage()}", previous: $e);
        }

        $payload = $response->json();

        if (($payload['result'] ?? null) !== 'success' || ! isset($payload['conversion_rates'])) {
            throw new RuntimeException('Exchange rate API returned an unexpected response.');
        }

        $rates = array_intersect_key($payload['conversion_rates'], array_flip($currencies));

        return Currency::updateOrCreate(
            ['exchange_rate_date' => now()->toDateString()],
            ['exchange_rate' => json_encode($rates)],
        );
    }
}
