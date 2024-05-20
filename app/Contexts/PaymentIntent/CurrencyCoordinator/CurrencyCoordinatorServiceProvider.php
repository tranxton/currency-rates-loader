<?php

declare(strict_types=1);

namespace App\Contexts\PaymentIntent\CurrencyCoordinator;

use App\Contexts\PaymentIntent\Contracts\CurrencyCoordinatorContract;
use App\Contexts\PaymentIntent\CurrencyCoordinator\Contracts\GetCurrencyRatesRepositoryContract;
use App\Contexts\PaymentIntent\CurrencyCoordinator\HttpClients\Fixer\CurrencyRateHttpClient;
use App\Contexts\PaymentIntent\CurrencyCoordinator\HttpClients\Fixer\CurrencyRateHttpClientAdapter;
use App\Contexts\PaymentIntent\CurrencyCoordinator\Repositories\CurrencyRate\Contracts\CurrencyRateRepositoryContract;
use App\Contexts\PaymentIntent\CurrencyCoordinator\Repositories\CurrencyRate\CurrencyRateRepository;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class CurrencyCoordinatorServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(GetCurrencyRatesRepositoryContract::class, CurrencyRateHttpClientAdapter::class);

        $this->app
            ->when(CurrencyRateHttpClient::class)
            ->needs('$apiUrl')
            ->giveConfig('contexts.payment-intent.currency-coordinator.http-client.fixer.api.url');

        $this->app
            ->when(CurrencyRateHttpClient::class)
            ->needs('$apiKey')
            ->giveConfig('contexts.payment-intent.currency-coordinator.http-client.fixer.api.key');

        $this->registerAggregateRoot();
        $this->registerRepositories();
    }

    private function registerAggregateRoot(): void
    {
        $this->app->singleton(
            abstract: CurrencyCoordinatorContract::class,
            concrete: CurrencyCoordinator::class
        );
    }

    private function registerRepositories(): void
    {
        $this->app->singleton(
            abstract: CurrencyRateRepositoryContract::class,
            concrete: CurrencyRateRepository::class
        );
    }

    /**
     * @return string[]
     */
    public function provides(): array
    {
        return [
            CurrencyCoordinatorContract::class,

            CurrencyRateRepositoryContract::class,
        ];
    }
}
