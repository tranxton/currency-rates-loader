<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\UseCases\LoadCurrencyRates\Factories;

use App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\Dto\CurrencyRateDto;
use App\Contexts\PaymentIntent\CurrencyCoordinator\Models\CurrencyRate\CurrencyRate;
use App\SharedKernel\Services\Guid\Dto\Guid;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

final readonly class CurrencyRateFactory
{
    public function make(CarbonImmutable $actualAt, CurrencyRateDto $currencyRateDto): CurrencyRate
    {
        $currencyRate = new CurrencyRate();

        $currencyRate->guid        = new Guid();
        $currencyRate->base_code   = $currencyRateDto->baseCurrencyCode;
        $currencyRate->target_code = $currencyRateDto->targetCurrencyCode;
        $currencyRate->rate        = $currencyRateDto->rate;
        $currencyRate->actual_at   = $actualAt;
        $currencyRate->created_at  = Carbon::now();

        return $currencyRate;
    }
}
