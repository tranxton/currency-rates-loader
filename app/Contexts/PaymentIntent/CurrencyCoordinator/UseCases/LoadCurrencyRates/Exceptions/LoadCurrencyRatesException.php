<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\UseCases\LoadCurrencyRates\Exceptions;

use App\SharedKernel\Enums\Currency\CurrencyCodeEnum;
use App\SharedKernel\Traits\Exceptions\WithPrevious;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;
use RuntimeException;
use Throwable;

final class LoadCurrencyRatesException extends RuntimeException implements Arrayable
{
    use WithPrevious;

    /**
     * @param CurrencyCodeEnum[] $targetCurrencyCodes
     */
    public function __construct(
        string $message,
        public ?CarbonImmutable $actualAt = null,
        public ?CurrencyCodeEnum $baseCurrencyCode = null,
        public ?array $targetCurrencyCodes = null,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function toArray(): array
    {
        return $this->withPrevious(
            [
                'message'               => $this->message,
                'actual_at'             => $this->actualAt?->toArray(),
                'base_currency_code'    => $this->baseCurrencyCode?->value,
                'target_currency_codes' => array_map(
                    static fn (CurrencyCodeEnum $targetCurrencyCode) => $targetCurrencyCode->value,
                    $this->targetCurrencyCodes ?? []
                ),
            ]
        );
    }
}
