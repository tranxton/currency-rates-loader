<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\Dto;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;

final readonly class CurrencyRatesDto implements Arrayable
{
    public CarbonImmutable $actualAt;

    /**
     * @var CurrencyRateDto[]
     */
    public array $rates;

    public function __construct(
        CarbonImmutable $actualAt,
        CurrencyRateDto ...$rates,
    ) {
        $this->actualAt = $actualAt;
        $this->rates    = $rates;
    }

    public function toArray(): array
    {
        return [
            'actual_at' => $this->actualAt->toArray(),
            'rates'     => array_map(
                static fn (CurrencyRateDto $currencyRateDto) => $currencyRateDto->toArray(),
                $this->rates
            ),
        ];
    }
}
