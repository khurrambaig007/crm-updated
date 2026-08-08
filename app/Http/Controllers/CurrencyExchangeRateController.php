<?php

namespace App\Http\Controllers;

use App\DataTables\CurrencyExchangeRatesDataTable;
use App\Http\Requests\CurrencyExchangeRate\UpdateCurrencyExchangeRateRequest;
use App\Models\Currency;
use App\Services\ExchangeRateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class CurrencyExchangeRateController extends Controller
{
    public function index(Request $request, CurrencyExchangeRatesDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('currency-exchange-rates.index');
    }

    public function fetch(ExchangeRateService $service): RedirectResponse
    {
        try {
            $currency = $service->fetchAndStore();
        } catch (Throwable $e) {
            Log::error('Failed to fetch exchange rates: '.$e->getMessage());

            return redirect()->route('currency-exchange-rates.index')->with('error', 'Failed to fetch exchange rates. Please try again later.');
        }

        return redirect()->route('currency-exchange-rates.index')->with('status', "Exchange rates for {$currency->exchange_rate_date->format('M j, Y')} saved successfully.");
    }

    public function edit(Currency $currency): View
    {
        return view('currency-exchange-rates.edit', [
            'currency' => $currency,
            'rates' => json_decode($currency->exchange_rate ?? '', true) ?: [],
        ]);
    }

    public function update(UpdateCurrencyExchangeRateRequest $request, Currency $currency): RedirectResponse
    {
        $validated = $request->validated();

        $rates = collect(ExchangeRateService::CURRENCIES)
            ->mapWithKeys(fn (string $code) => [$code => $validated[$code] ?? null])
            ->reject(fn ($value) => $value === null || $value === '')
            ->map(fn ($value) => (float) $value)
            ->all();

        $currency->update(['exchange_rate' => json_encode($rates)]);

        return redirect()->route('currency-exchange-rates.index')->with('status', 'Exchange rate updated successfully.');
    }
}
