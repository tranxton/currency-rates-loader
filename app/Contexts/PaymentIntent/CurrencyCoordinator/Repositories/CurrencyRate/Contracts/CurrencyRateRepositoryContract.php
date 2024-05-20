<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\Repositories\CurrencyRate\Contracts;

use App\Contexts\PaymentIntent\CurrencyCoordinator\Models\CurrencyRate\CurrencyRate;
use App\SharedKernel\Repositories\Exceptions\StoreException;
use App\SharedKernel\Repositories\SearchQuery\Contracts\SearchableRepositoryContract;

interface CurrencyRateRepositoryContract extends SearchableRepositoryContract
{
    /**
     * @return CurrencyRate[]
     *
     * @throws StoreException
     */
    public function storeMany(CurrencyRate ...$currencyRate): array;
}
