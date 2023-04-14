<?php

namespace Mazi\CurrencyConverter;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/api.php');
        $this->mergeConfigFrom(
            __DIR__.'/../config/cur-converter.php',
            'cur-converter'
        );
        $this->publishes([
            __DIR__.'/../config/cur-converter.php' => config_path('cur-converter.php'),
        ]);
    }
}
