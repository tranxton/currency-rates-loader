<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\HttpClients\Fixer\Factories;

use App\Contexts\PaymentIntent\CurrencyCoordinator\HttpClients\Fixer\Dto\CurrencyRateDto;
use App\SharedKernel\Enums\Currency\CurrencyCodeEnum;

final readonly class CurrencyRateDtoFactory
{
    public function make(string $baseCurrencyCode, string $targetCurrencyCode, float $rate): CurrencyRateDto
    {
        return new CurrencyRateDto(
            CurrencyCodeEnum::from($baseCurrencyCode),
            CurrencyCodeEnum::from($targetCurrencyCode),
            $rate
        );
    }
}
