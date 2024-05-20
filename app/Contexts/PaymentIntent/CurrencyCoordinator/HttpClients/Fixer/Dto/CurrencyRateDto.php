<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\HttpClients\Fixer\Dto;

use App\SharedKernel\Enums\Currency\CurrencyCodeEnum;
use Illuminate\Contracts\Support\Arrayable;

final readonly class CurrencyRateDto implements Arrayable
{
    public function __construct(
        public CurrencyCodeEnum $baseCurrencyCode,
        public CurrencyCodeEnum $targetCurrencyCode,
        public float $rate,
    ) {
    }

    public function toArray(): array
    {
        return [
            'base_currency_code'   => $this->baseCurrencyCode->value,
            'target_currency_code' => $this->targetCurrencyCode->value,
            'rate'                 => $this->rate,
        ];
    }
}
