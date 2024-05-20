<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\UseCases\LoadCurrencyRates;

use App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\Dto\CurrencyRateDto;
use App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\Dto\CurrencyRatesDto;
use App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\Dto\CurrencyRatesFilterDto;
use App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\GetCurrencyRatesRepositoryContract;
use App\Contexts\PaymentIntent\CurrencyCoordinator\Models\CurrencyRate\CurrencyRate;
use App\Contexts\PaymentIntent\CurrencyCoordinator\Repositories\CurrencyRate\Contracts\CurrencyRateRepositoryContract;
use App\Contexts\PaymentIntent\CurrencyCoordinator\UseCases\LoadCurrencyRates\Exceptions\LoadCurrencyRatesException;
use App\Contexts\PaymentIntent\CurrencyCoordinator\UseCases\LoadCurrencyRates\Factories\CurrencyRateFactory;
use App\SharedKernel\Enums\Currency\CurrencyCodeEnum;
use App\SharedKernel\Services\HttpClient\Exceptions\HttpClientException;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class LoadCurrencyRatesUseCase
{
    public function __construct(
        private GetCurrencyRatesRepositoryContract $getCurrencyRatesRepository,
        private CurrencyRateRepositoryContract $currencyRateRepository,
        private CurrencyRateFactory $currencyRateFactory,
    ) {
    }

    /**
     * @throws LoadCurrencyRatesException
     */
    public function run(CarbonImmutable $actualAt): void
    {
        foreach (CurrencyCodeEnum::cases() as $baseCurrencyCode) {
            $currencyRatesFilterDto = $this->makeCurrencyRatesFilterDto($actualAt, $baseCurrencyCode);
            $currencyRatesDto       = $this->getCurrencyRates($currencyRatesFilterDto);
            $currencyRates          = $this->makeCurrencyRates($currencyRatesDto);

            $this->storeCurrencyRates(...$currencyRates);
        }
    }

    private function makeCurrencyRatesFilterDto(
        CarbonImmutable $actualAt,
        CurrencyCodeEnum $baseCurrencyCode,
    ): CurrencyRatesFilterDto {
        return new CurrencyRatesFilterDto(
            $actualAt,
            $baseCurrencyCode,
            ...$this->getTargetCurrencyCodesExpect($baseCurrencyCode)
        );
    }

    /**
     * @return CurrencyCodeEnum[]
     */
    private function getTargetCurrencyCodesExpect(CurrencyCodeEnum $baseCurrencyCode): array
    {
        $targetCodes = CurrencyCodeEnum::cases();

        $baseCurrencyCodeIndex = array_search($baseCurrencyCode, $targetCodes, true);
        Arr::forget($targetCodes, $baseCurrencyCodeIndex);

        return $targetCodes;
    }

    /**
     * @throws LoadCurrencyRatesException
     */
    private function getCurrencyRates(CurrencyRatesFilterDto $currencyRatesFilterDto): CurrencyRatesDto
    {
        try {
            return $this->getCurrencyRatesRepository->getCurrencyRates($currencyRatesFilterDto);
        } catch (HttpClientException $e) {
            throw new LoadCurrencyRatesException(
                message:             $e->getMessage(),
                actualAt:            $currencyRatesFilterDto->actualAt,
                baseCurrencyCode:    $currencyRatesFilterDto->baseCurrencyCode,
                targetCurrencyCodes: $currencyRatesFilterDto->targetCurrencyCodes,
                previous:            $e
            );
        }
    }

    /**
     * @return CurrencyRate[]
     */
    private function makeCurrencyRates(CurrencyRatesDto $currencyRatesDto): array
    {
        return array_map(
            fn (CurrencyRateDto $currencyRateDto) => $this->currencyRateFactory->make(
                $currencyRatesDto->actualAt,
                $currencyRateDto
            ),
            $currencyRatesDto->rates
        );
    }

    /**
     * @throws LoadCurrencyRatesException
     */
    private function storeCurrencyRates(CurrencyRate ...$currencyRates): void
    {
        try {
            DB::transaction(fn () => $this->currencyRateRepository->storeMany(...$currencyRates));
        } catch (Throwable $e) {
            throw new LoadCurrencyRatesException(
                message:  $e->getMessage(),
                previous: $e
            );
        }
    }
}
