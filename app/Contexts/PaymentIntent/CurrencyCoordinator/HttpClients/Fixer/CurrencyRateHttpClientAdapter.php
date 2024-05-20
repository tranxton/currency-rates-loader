<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\HttpClients\Fixer;

use App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\Dto\CurrencyRateDto as CurrencyRateDtoContract;
use App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\Dto\CurrencyRatesDto;
use App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\Dto\CurrencyRatesFilterDto;
use App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\GetCurrencyRatesRepositoryContract;
use App\Contexts\PaymentIntent\CurrencyCoordinator\HttpClients\Fixer\Dto\CurrencyRateDto;
use App\SharedKernel\Services\MoneyFormatter\MoneyFormatter;

final readonly class CurrencyRateHttpClientAdapter implements GetCurrencyRatesRepositoryContract
{
    public function __construct(
        private CurrencyRateHttpClient $currencyRateClient,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getCurrencyRates(CurrencyRatesFilterDto $filterDto): CurrencyRatesDto
    {
        $currencyRates = $this->currencyRateClient->getCurrencyRates(
            $filterDto->actualAt,
            $filterDto->baseCurrencyCode,
            ...$filterDto->targetCurrencyCodes
        );

        return new CurrencyRatesDto(
            $currencyRates->date,
            ...$this->convertRates(...$currencyRates->rates)
        );
    }

    /**
     * @return CurrencyRateDtoContract[]
     */
    private function convertRates(CurrencyRateDto ...$currencyRates): array
    {
        return array_map(
            static fn (CurrencyRateDto $currencyRateDto) => new CurrencyRateDtoContract(
                $currencyRateDto->baseCurrencyCode,
                $currencyRateDto->targetCurrencyCode,
                MoneyFormatter::numberToInt($currencyRateDto->rate)
            ),
            $currencyRates
        );
    }
}
