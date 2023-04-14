<?php

namespace App\Providers;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\ServiceProvider;
use Mazi\CurrencyConverter\Contracts\Converter as ContractsConverter;
use Mazi\CurrencyConverter\Contracts\ConverterDriver;
use Mazi\CurrencyConverter\Contracts\Parser;
use Mazi\CurrencyConverter\Converter;
use Mazi\CurrencyConverter\Drivers\EuroBankDriver;
use Mazi\CurrencyConverter\Parsers\XMLParser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerCurrencyConverter();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }

    private function registerCurrencyConverter(): void
    {
        $this->app->bind(
            Parser::class,
            XMLParser::class
        );

        $this->app->singleton(ConverterDriver::class, function () {
            return new EuroBankDriver(
                new \GuzzleHttp\Client(),
                $this->app->make(Parser::class),
                $this->app->make(Repository::class)
            );
        });

        $this->app->singleton(ContractsConverter::class, function() {
            return new Converter(
                $this->app->make(ConverterDriver::class
            ));
        });
    }
}
