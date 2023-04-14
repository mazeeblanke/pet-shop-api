<?php

namespace Mazi\CurrencyConverter;

use Exception;
use GuzzleHttp\ClientInterface;
use Illuminate\Foundation\Application;
use Mazi\CurrencyConverter\Contracts\Parser;
use Mazi\CurrencyConverter\Drivers\EuroBankDriver;

class ConverterFactory
{
    /**
     * Supported drivers
     */
    protected const DRIVERS = [
        'eurobankDriver' => EuroBankDriver::class,
    ];

    public static function make(string $driverName, ClientInterface $http, Application $app)
    {
        // get driver from list of supported drivers
        if (! isset(self::DRIVERS[$driverName])) {
            throw new Exception("{$driverName} is not a supported driver.");
        }

        // initialize it with dependencies
        $driverClass = self::DRIVERS[$driverName];

        $parser = $app->make(Parser::class);

        // return instance of Converter class
        return new Converter(new $driverClass($http, $parser));
    }

    /**
     * List available drivers.
     *
     * @return array
     */
    public function drivers(): array
    {
        return self::DRIVERS;
    }
}
