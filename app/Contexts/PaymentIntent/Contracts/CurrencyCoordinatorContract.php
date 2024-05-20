<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\Contracts;

use Carbon\CarbonImmutable;

interface CurrencyCoordinatorContract
{
    public function loadCurrencyRatesOnDate(CarbonImmutable $actualAt): void;
}
