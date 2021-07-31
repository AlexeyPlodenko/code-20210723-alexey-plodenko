<?php

namespace App\Events;

use App\Models\ExchangeRate;
use Exception;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GotExchangeRates implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public string $message = '{}';

    /**
     * @var array
     */
    protected array $rates;

    /**
     * @param ExchangeRate[] $rates
     * @throws Exception
     */
    public function setRates(array $rates): void
    {
        assert(!array_filter($rates, function($rate) {
            return !(is_object($rate) && is_a($rate, ExchangeRate::class));
        }));

        $this->rates = $rates;
        $this->makeMessage();
    }

    /**
     * @return string[]
     */
    public function broadcastOn(): array
    {
        return ['my-channel'];
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'my-event';
    }

    /**
     * makeMessage.
     */
    protected function makeMessage(): void
    {
        $res = [
            'rates' => []
        ];

        $rates =& $res['rates'];
        foreach ($this->rates as $rate) {
            /** @var ExchangeRate $rate */
            $rates[$rate->getCurrency()] = $rate->getPrice();
        }

        $this->message = json_encode($res);
    }
}
