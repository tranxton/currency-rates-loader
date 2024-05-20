<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator\HttpClients\Fixer;

use App\Contexts\PaymentIntent\CurrencyCoordinator\HttpClients\Fixer\Dto\CurrencyRatesDto;
use App\Contexts\PaymentIntent\CurrencyCoordinator\HttpClients\Fixer\Factories\CurrencyRatesDtoFactory;
use App\SharedKernel\Enums\Currency\CurrencyCodeEnum;
use App\SharedKernel\Enums\DefaultDateTimeFormatEnum;
use App\SharedKernel\Services\HttpClient\BaseHttpClient;
use App\SharedKernel\Services\HttpClient\Dto\RequestPayloadDto;
use App\SharedKernel\Services\HttpClient\Dto\ResponsePayloadDto;
use App\SharedKernel\Services\HttpClient\Exceptions\HttpClientException;
use Carbon\CarbonImmutable;

final class CurrencyRateHttpClient extends BaseHttpClient
{
    public function __construct(
        private readonly string $apiUrl,
        private readonly string $apiKey,
        private readonly CurrencyRatesDtoFactory $currencyRatesDtoFactory,
    ) {
    }

    /**
     * @throws HttpClientException
     */
    public function getCurrencyRates(
        CarbonImmutable $date,
        CurrencyCodeEnum $baseCurrencyCode,
        CurrencyCodeEnum ...$targetCurrencyCodes,
    ): CurrencyRatesDto {
        $url         = sprintf("%s%s", $this->apiUrl, $date->format(DefaultDateTimeFormatEnum::Date->value));
        $queryParams = [
            'access_key' => $this->apiKey,
            'base'       => $baseCurrencyCode->value,
            'symbols'    => $this->makeImplodedTargetCurrencyCodes(...$targetCurrencyCodes),
        ];

        $response     = $this->get($url, $queryParams);
        $responseBody = $response->json();

        if ($this->isBadResponse($responseBody)) {
            throw new HttpClientException(
                message:         'Got bad response from fixer.io',
                requestPayload:  new RequestPayloadDto(url: $url),
                responsePayload: new ResponsePayloadDto(
                    code:    $response->status(),
                    headers: $response->headers(),
                    body:    $response->body()
                ),
            );
        }

        return $this->currencyRatesDtoFactory->make($responseBody);
    }

    private function makeImplodedTargetCurrencyCodes(CurrencyCodeEnum ...$targetCurrencyCodes): string
    {
        return implode(
            ',',
            array_map(
                static fn (CurrencyCodeEnum $targetCurrencyCode) => $targetCurrencyCode->value,
                $targetCurrencyCodes
            )
        );
    }

    /**
     * @param array{
     *     success: bool,
     *     timestamp: int,
     *     historical: bool,
     *     base: string,
     *     date: string,
     *     rates:array<string, float>
     *         } $responseBody
     */
    private function isBadResponse(array $responseBody): bool
    {
        return !array_key_exists('success', $responseBody) || ((bool) $responseBody['success']) === false;
    }
}
