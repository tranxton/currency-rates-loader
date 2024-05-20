<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\Dto;

use App\SharedKernel\Enums\Currency\CurrencyCodeEnum;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;

final readonly class CurrencyRatesFilterDto implements Arrayable
{
    public CarbonImmutable  $actualAt;
    public CurrencyCodeEnum $baseCurrencyCode;
    /**
     * @var CurrencyCodeEnum[] $targetCurrencyCodes
     */
    public array $targetCurrencyCodes;

    public function __construct(
        CarbonImmutable $actualAt,
        CurrencyCodeEnum $baseCurrencyCode,
        CurrencyCodeEnum ...$targetCurrencyCodes,
    ) {
        $this->actualAt            = $actualAt;
        $this->baseCurrencyCode    = $baseCurrencyCode;
        $this->targetCurrencyCodes = $targetCurrencyCodes;
    }

    public function toArray(): array
    {
        return [
            'base_currency_code'    => $this->baseCurrencyCode->value,
            'target_currency_codes' => array_map(
                static fn (CurrencyCodeEnum $targetCurrencyCode) => $targetCurrencyCode->value,
                $this->targetCurrencyCodes
            ),
        ];
    }
}
