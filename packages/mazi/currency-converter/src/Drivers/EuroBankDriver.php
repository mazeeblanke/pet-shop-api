<?php

namespace Mazi\CurrencyConverter\Drivers;

use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mazi\CurrencyConverter\Contracts\ConverterDriver;
use Mazi\CurrencyConverter\Contracts\Parser;
use Mazi\CurrencyConverter\Exceptions\CurrencyNotFound;
use Mazi\CurrencyConverter\Exceptions\UnsupportedCurrencySymbol;
use Mazi\CurrencyConverter\Symbol;
use Psr\Http\Message\ResponseInterface;

class EuroBankDriver implements ConverterDriver
{
    /**
     * The api url for driver
     */
    protected string $apiURL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    /**
     * http client
     */
    protected ClientInterface $http;

    /**
     * The base currency
     */
    protected string $baseCurrency = Symbol::EUR;

    /**
     * The last time checked
     */
    protected string $lastDate;

    /**
     * rates
     *
     * @var string $baseCurrency
     */
    protected array $rates;

    /**
     * Parser that does the work of parsing
     */
    protected Parser $parser;

    /**
     * Cache service
     *
     * @var [type]
     */
    protected CacheRepository $cache;

    /**
     * Config service
     *
     * @var [type]
     */
    protected ConfigRepository $config;

    /**
     * List of symbols supported by driver
     *
     * @var array
     */
    protected array $supportedSymbols = [
        Symbol::USD,
        Symbol::JPY,
        Symbol::BGN,
        Symbol::CZK,
        Symbol::DKK,
        Symbol::GBP,
        Symbol::HUF,
        Symbol::PLN,
        Symbol::RON,
        Symbol::SEK,
        Symbol::CHF,
        Symbol::ISK,
        Symbol::TRY,
        Symbol::AUD,
        Symbol::BRL,
        Symbol::CAD,
        Symbol::CNY,
        Symbol::HKD,
        Symbol::IDR,
        Symbol::ILS,
        Symbol::INR,
        Symbol::KRW,
        Symbol::MXN,
        Symbol::MYR,
        Symbol::NZD,
        Symbol::PHP,
        Symbol::SGD,
        Symbol::THB,
        Symbol::ZAR,
    ];

    public function __construct(
        ClientInterface $http,
        Parser $parser,
        CacheRepository $cache,
        ConfigRepository $config
    ) {
        $this->http = $http;
        $this->parser = $parser;
        $this->cache = $cache;
        $this->config = $config;
    }

    /**
     * Process the conversion
     */
    public function process(string $targetCurrency, string $amount): float
    {
        $targetCurrency = strtoupper($targetCurrency);

        // check if target currency is supported
        if (! in_array($targetCurrency, $this->supportedSymbols)) {
            throw new UnsupportedCurrencySymbol(
                'Driver does not support symbol ' . $targetCurrency
            );
        }

        $cachedData = $this->cache->get(
            $this->config->get('cur-converter.cache-key'),
            []
        );

        $this->lastDate = $cachedData['lastRefresh'] ?? '';
        $this->rates = $cachedData['rates'] ?? [];

        // if not already set and still valid
        if (! isset($this->rates) || ! $this->isValid()) {
            $this->parseData(
                $this->getData()->getBody()
            );
        }

        $currency = $this->getRate($targetCurrency);

        // multiply by amout and return
        return (float) $currency['rate'] * (float) $amount;
    }

    /**
     * Make api request to fetch data
     *
     * @return  ResponseInterface
     */
    protected function getData(): ResponseInterface
    {
        return $this->http
            ->request(
                'GET',
                $this->apiURL
            );
    }

    /**
     * parse xml data and return array
     *
     * @param   string  $xml
     *
     * @return  array
     */
    protected function parseData(string $xml = "kkkk"): array
    {
        // parse it to get all rates
        $data = $this->parser->parse($xml);

        $this->lastDate = $data['Cube']['Cube']['@attributes']['time'];

        $this->rates = array_map(
            static fn ($arr) => $arr['@attributes'],
            $data['Cube']['Cube']['Cube']
        );

        $this->cache->put(
            $this->config->get('cur-converter.cache-key'),
            [
                'rates' => $this->rates,
                'lastRefresh' => $this->lastDate,
            ],
            $this->config->get('cur-converter.cache-time')
        );

        return $this->rates;
    }

    /**
     * Get an array of currency and rate
     *
     * @param   string $targetCurrency
     *
     * @return  array
     */
    protected function getRate(string $targetCurrency): array
    {
        $currency = array_filter(
            $this->rates,
            static fn ($arr) => $arr['currency'] === $targetCurrency
        );

        // if no results
        if (count($currency) === 0) {
            throw new CurrencyNotFound(
                'Unable to find currency ' . $targetCurrency
            );
        }

        return reset($currency);
    }

    /**
     * Determin if the last fetched data is still valid
     *
     * @return  bool
     */
    protected function isValid(): bool
    {
        return isset($this->lastDate) &&
        $this->lastDate === date('Y-m-d');
    }
}
