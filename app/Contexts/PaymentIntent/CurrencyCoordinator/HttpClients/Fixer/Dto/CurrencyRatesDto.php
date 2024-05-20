<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\HttpClients\Fixer\Dto;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;

final readonly class CurrencyRatesDto implements Arrayable
{
    public CarbonImmutable $date;

    /**
     * @var CurrencyRateDto[]
     */
    public array $rates;

    public function __construct(
        CarbonImmutable $date,
        CurrencyRateDto ...$rates,
    ) {
        $this->date  = $date;
        $this->rates = $rates;
    }

    public function toArray(): array
    {
        return [
            'date'  => $this->date->toArray(),
            'rates' => array_map(
                static fn (CurrencyRateDto $currencyRateDto) => $currencyRateDto->toArray(),
                $this->rates
            ),
        ];
    }
}
