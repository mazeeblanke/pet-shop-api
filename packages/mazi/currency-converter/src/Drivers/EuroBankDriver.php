<?php

namespace Mazi\CurrencyConverter\Drivers;

use Exception;
use GuzzleHttp\ClientInterface;
use Mazi\CurrencyConverter\Contracts\ConverterDriver;
use Mazi\CurrencyConverter\Contracts\Parser;
use Mazi\CurrencyConverter\Symbol;

class EuroBankDriver implements ConverterDriver
{
    /**
     * The api url for driver
     *
     * @var string
     */
    protected string $apiURL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    /**
     * http client
     *
     * @var ClientInterface
     */
    protected ClientInterface $http;

    /**
     * The base currency
     *
     * @var string $baseCurrency
     *
     */
    protected string $baseCurrency = Symbol::EUR;

    /**
     * The last time checked
     *
     * @var string $baseCurrency
     *
     */
    protected string $lastDate;

    /**
     * rates
     *
     * @var string $baseCurrency
     *
     */
    protected array $rates;

    /**
     * Parser that does the work of parsing
     *
     * @var Parser
     */
    protected Parser $parser;

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
        Symbol::ZAR
    ];

    public function __construct(ClientInterface $http, Parser $parser)
    {
        $this->http = $http;
        $this->parser = $parser;
    }

    /**
     * Process the conversion
     *
     * @param   string  $targetCurrency
     * @param   string  $amount
     *
     * @return  string
     */
    public function process($targetCurrency, $amount): string
    {
        // check if target curreny is supported
        if(!in_array($targetCurrency, $this->supportedSymbols)) {
            // throw error
        }

        // if not already set and still valid
        if (!isset($this->rates) || !$this->isValid()) {
            $this->parseData(
                $this->getData()
            );
        }

        $currency = $this->getRate($targetCurrency);

        // multiply by amout and return
        return $currency['rate'] * $amount;
    }

    protected function getData()
    {
        $response = $this->http
            ->request(
                'GET',
                $this->apiURL
            );

        return $response->getBody();
    }

    protected function parseData($xml)
    {
        // parse it to get all rates
        $data = $this->parser->parse($xml);

        $this->lastDate = $data['Cube']['Cube']['@attributes']['time'];

        $this->rates = array_map(
            fn($arr) => $arr['@attributes'],
            $data['Cube']['Cube']['Cube']
        );

        return $this->rates;
    }

    protected function getRate($targetCurrency)
    {
        $currency = array_filter(
            $this->rates,
            fn($arr)=> $arr['currency'] == strtoupper($targetCurrency)
        );

        // if no results
        if (count($currency) == 0) {
            // throw error
        }

        return reset($currency);
    }

    protected function isValid()
    {
        return isset($this->lastDate) &&
        $this->lastDate == date('Y-m-d');
    }
}
