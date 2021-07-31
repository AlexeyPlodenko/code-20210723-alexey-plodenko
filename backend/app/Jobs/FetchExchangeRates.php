<?php

namespace App\Jobs;

use App\Events\GotExchangeRates;
use App\Models\ExchangeRate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use stdClass;

class FetchExchangeRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rates = $this->fetchRates();
        if (!$rates) {
            return;
        }

        $gotExchangeRates = new GotExchangeRates();
        $gotExchangeRates->setRates($rates);
        event($gotExchangeRates);
    }

    /**
     * @return array|null
     */
    protected function fetchRates(): array|null
    {
        $host = config('app.cmc.host');
        $apiKey = config('app.cmc.apiKey');

        $url = 'https://'. $host .'/v1/cryptocurrency/quotes/latest';
        $parameters = [
            'symbol' => 'BTC',
        ];
        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: '. $apiKey
        ];
        $qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL

        $curl = curl_init(); // Get cURL resource

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $request,            // set the request URL
            CURLOPT_HTTPHEADER => $headers,     // set the headers
            CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ]);

        $resp = curl_exec($curl); // Send the request, save the response
        curl_close($curl); // Close request

        // validate
        $rates = json_decode($resp);
        if (!$this->isRatesValid($rates)) {
            return null;
        }

        return $this->normalizeRates($rates);
    }

    /**
     * @param object $rates
     * @return bool
     */
    protected function isRatesValid(object $rates): bool
    {
        return (
               isset($rates->status->error_code)
            && !$rates->status->error_code // should not contain errors
            && isset($rates->data->BTC->quote->USD->price)
            && is_float($rates->data->BTC->quote->USD->price)
            && $rates->data->BTC->quote->USD->price > 0 // should contain valid price
        );
    }

    /**
     * @param object $rates
     * @return array
     */
    protected function normalizeRates(object $rates): array
    {
        $normalized = [];

        $rate = new ExchangeRate();
        $rate->setCurrency('USD');
        $rate->setPrice($rates->data->BTC->quote->USD->price);
        $normalized[] = $rate;

        return $normalized;
    }
}
