<?php

use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mazi\CurrencyConverter\Contracts\Parser;
use Mazi\CurrencyConverter\Drivers\EuroBankDriver;
use Mazi\CurrencyConverter\Exceptions\UnsupportedCurrencySymbol;
use Mazi\CurrencyConverter\Symbol;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;

class EuroBankDriverTest extends TestCase
{
    /** @test */
    function throws_error_when_unsupported_target_currency()
    {
        $http = $this->getMockForAbstractClass(ClientInterface::class);
        $parser = $this->getMockForAbstractClass(Parser::class);
        $cache = $this->getMockForAbstractClass(CacheRepository::class);
        $config = $this->getMockForAbstractClass(ConfigRepository::class);

        $euroBankDriver = new EuroBankDriver(
            $http,
            $parser,
            $cache,
            $config
        );

        $this->expectException(UnsupportedCurrencySymbol::class);
        $euroBankDriver->process("WRONG", '300');
    }

    // /** @test */
    // function data_not_fetched_and_parsed_when_valid_cache_data_exist()
    // {
    // }

    /** @test*/
    function converts_amount_for_supported_currency()
    {
        $targetCurrency = Symbol::USD;
        $amount = 1.2;

        // Mocking dependencies
        $configMock = $this->createConfigMock();

        $httpMock = $this->createHttpMock();

        $parserMock = $this->createParserMock();

        $cacheMock = $this->createCacheMock($configMock);

        $driver = new EuroBankDriver(
            $httpMock,
            $parserMock,
            $cacheMock,
            $configMock
        );
        $result = $driver->process($targetCurrency, $amount);

        $this->assertSame($amount * 1.2, $result);
    }

    /** @test */
    public function cached_data_is_used_when_available_instead_of_fetching_fresh_data()
    {
        $targetCurrency = Symbol::USD;
        $amount = 1.2;

        $configMock = $this->createConfigMock();
        $cacheMock = $this->createCacheMock($configMock);

        $httpMock = $this->createHttpMock();

        $httpMock->expects($this->never())
        ->method('request');

        $parserMock = $this->createParserMock();

        $driver = new EuroBankDriver(
            $httpMock,
            $parserMock,
            $cacheMock,
            $configMock
        );
        $result = $driver->process($targetCurrency, $amount);

        $this->assertSame($amount * 1.2, $result);
    }

    private function createHttpMock(): ClientInterface
    {
        $httpMock = $this->createMock(ClientInterface::class);

        $httpMock->method('request')
            ->willReturn(
                new Response(
                    200,
                    [],
                    '<?xml version="1.0" encoding="UTF-8"?>'
                )
            );

        return $httpMock;
    }

    private function createConfigMock(): ConfigRepository
    {
        $configMock = $this->getMockForAbstractClass(
            ConfigRepository::class
        );

        $configMock->method('get')
            ->with($this->logicalOr(
                $this->equalTo('cur-converter.cache-key'),
                $this->equalTo('cur-converter.cache-time')
            ))
            ->will($this->returnCallback(array($this, 'loadConfig')));

        return $configMock;
    }

    private function createCacheMock($configMock): CacheRepository
    {
        $cacheMock = $this->createMock(CacheRepository::class);

        $cacheMock->method('put')
            ->with(
                $configMock->get('cur-converter.cache-key'),
                [
                    'rates' => [
                        ['currency' => Symbol::USD, 'rate' => '1.2'],
                        ['currency' => Symbol::EUR, 'rate' => '1'],
                    ],
                    'lastRefresh' => '2023-04-13',
                ],
                $configMock->get('cur-converter.cache-time')
            );

        $cacheMock
            ->method('get')
            ->with($configMock->get('cur-converter.cache-key'))
            ->willReturn(                [
                'rates' => [
                    ['currency' => Symbol::USD, 'rate' => '1.2'],
                    ['currency' => Symbol::EUR, 'rate' => '1'],
                ],
                'lastRefresh' => date('Y-m-d'),
            ]);

        return  $cacheMock;
    }

    private function createParserMock(): Parser
    {
        $parserMock = $this->createMock(Parser::class);

        $parserMock->method('parse')
            ->willReturn([
                'Cube' => [
                    'Cube' => [
                        '@attributes' => ['time' => '2023-04-13'],
                        'Cube' => [
                            ['@attributes' =>
                                [
                                    'currency' => Symbol::USD,
                                    'rate' => '1.2'
                                ]
                            ],
                            ['@attributes' =>
                                [
                                    'currency' => Symbol::EUR,
                                    'rate' => '1'
                                ]
                            ],
                        ]
                    ]
                ]
            ]);

        return $parserMock;
    }

    public function loadConfig($config): string|int
    {
        if ($config === 'cur-converter.cache-key') {
            return 'CUR-CONVERTER-RATES';
        }

        if ($config === 'cur-converter.cache-time') {
            return 60;
        }
    }
}
