<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\HttpClients\Fixer\Factories;

use App\Contexts\PaymentIntent\CurrencyCoordinator\HttpClients\Fixer\Dto\CurrencyRatesDto;
use App\SharedKernel\Enums\DefaultDateTimeFormatEnum;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;

final readonly class CurrencyRatesDtoFactory
{
    public function __construct(
        private CurrencyRateDtoFactory $currencyRateDtoFactory,
    ) {
    }

    /**
     * @param array{
     *      success: bool,
     *      timestamp: int,
     *      historical: bool,
     *      base: string,
     *      date: string,
     *      rates:array<string, float>
     *          } $response
     *
     * @return CurrencyRatesDto
     */
    public function make(array $response): CurrencyRatesDto
    {
        $date             = Arr::get($response, 'date');
        $baseCurrencyCode = Arr::get($response, 'base');
        $rates            = Arr::get($response, 'rates');

        return new CurrencyRatesDto(
            CarbonImmutable::createFromFormat(DefaultDateTimeFormatEnum::Date->value, $date),
            ...array_map(
                fn (string $targetCurrencyCode, float $rate) => $this->currencyRateDtoFactory->make(
                    $baseCurrencyCode,
                    $targetCurrencyCode,
                    $rate
                ),
                array_keys($rates),
                $rates,
            )
        );
    }
}
