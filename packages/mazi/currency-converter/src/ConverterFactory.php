<?php

namespace Mazi\CurrencyConverter;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Foundation\Application;
use Mazi\CurrencyConverter\Contracts\Parser;
use Mazi\CurrencyConverter\Drivers\EuroBankDriver;
use Mazi\CurrencyConverter\Exceptions\DriverNotFound;
use Mazi\CurrencyConverter\Parsers\XMLParser;

class ConverterFactory
{
    /**
     * Supported drivers
     */
    protected const DRIVERS = [
        'eurobankDriver' => EuroBankDriver::class,
    ];

    /**
     * Default parsers
     */
    protected const DEFAULT_PARSERS = [
        'eurobankDriver' => XMLParser::class,
    ];

    public static function make(
        string $driverName,
        Application $app,
        ClientInterface $http = null,
        Parser $parser = null,
        Repository $cache = null
    ) {
        // get driver from list of supported drivers
        if (! isset(self::DRIVERS[$driverName])) {
            throw new DriverNotFound(
                "{$driverName} is not a supported driver."
            );
        }

        // initialize it with dependencies
        $driverClass = self::DRIVERS[$driverName];

        // If no hhtp client is specified, create a client instance.
        $http = $http == null ? new Client() : $http;

        // If no parser is specified, create default parser instance.
        $defaultParserClass = self::DEFAULT_PARSERS[$driverName];
        $parser = $parser == null ? new $defaultParserClass() : $parser;

        // If no cache is specified, create a cache instance.
        $cache = $cache == null ? $app->make(CacheRepository::class) : $cache;

        // return instance of Converter class
        return new Converter(
            new $driverClass($http, $parser, $cache)
        );
    }

    /**
     * List available drivers.
     *
     * @return array
     */
    public static function listDrivers(): array
    {
        return self::DRIVERS;
    }
}
