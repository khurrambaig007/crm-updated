<?php

namespace App\Console\Commands;

use App\Services\ExchangeRateService;
use Illuminate\Console\Command;
use Throwable;

class FetchExchangeRates extends Command
{
    protected $signature = 'exchange-rates:fetch';

    protected $description = 'Fetch the latest exchange rates from the API and store them for today';

    public function handle(ExchangeRateService $service): int
    {
        try {
            $currency = $service->fetchAndStore();
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Exchange rates saved for {$currency->exchange_rate_date->format('Y-m-d')}.");

        return self::SUCCESS;
    }
}
