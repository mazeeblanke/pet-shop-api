<?php

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\Application as FoundationApplication;
use Mazi\CurrencyConverter\Contracts\Converter;
use Mazi\CurrencyConverter\ConverterFactory;
use Mazi\CurrencyConverter\Exceptions\DriverNotFound;
use PHPUnit\Framework\TestCase;

class ConverterFactoryTest extends TestCase
{
    /** @test */
    public function can_init_driver()
    {

        $app = $this->createMock(FoundationApplication::class);

        $app->method('make')
            ->with($this->logicalOr(
                $this->equalTo(ConfigRepository::class),
                $this->equalTo(CacheRepository::class)
            ))
           ->will($this->returnCallback([$this, 'createMockInstance']));


        $this->assertInstanceOf(
            Converter::class,
            ConverterFactory::make(
                'eurobankDriver',
                $app
            )
        );
    }

    public function createMockInstance($class)
    {
        return $this->createMock($class);
    }

    /** @test */
    public function can_get_list_of_drivers()
    {
        $this->assertIsArray(
            ConverterFactory::listDrivers()
        );
        $this->assertArrayHasKey(
            'eurobankDriver',
            ConverterFactory::listDrivers()
        );
    }

    /** @test */
    public function will_throw_exception_if_using_invalid_driver()
    {
        $app = $this->createMock(FoundationApplication::class);
        $this->expectException(DriverNotFound::class);
        ConverterFactory::make('not-existing', $app);
    }
}
