<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator;

use App\Contexts\PaymentIntent\Contracts\CurrencyCoordinatorContract;
use App\Contexts\PaymentIntent\CurrencyCoordinator\UseCases\LoadCurrencyRates\LoadCurrencyRatesUseCase;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\App;

final readonly class CurrencyCoordinator implements CurrencyCoordinatorContract
{
    public function loadCurrencyRatesOnDate(CarbonImmutable $actualAt): void
    {
        App::make(LoadCurrencyRatesUseCase::class)->run($actualAt);
    }
}
