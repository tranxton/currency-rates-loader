<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts;

use App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\Dto\CurrencyRatesDto;
use App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\Dto\CurrencyRatesFilterDto;
use App\SharedKernel\Services\HttpClient\Exceptions\HttpClientException;

interface GetCurrencyRatesRepositoryContract
{
    /**
     * @throws HttpClientException
     */
    public function getCurrencyRates(CurrencyRatesFilterDto $filterDto): CurrencyRatesDto;
}
