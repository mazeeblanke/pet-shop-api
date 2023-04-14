<?php

use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Foundation\Application;
use Mazi\CurrencyConverter\Contracts\Converter;
use Mazi\CurrencyConverter\ConverterFactory;
use Mazi\CurrencyConverter\Exceptions\DriverNotFound;
use PHPUnit\Framework\TestCase;


class ConverterFactoryTest extends TestCase
{
    /** @test */
    public function can_init_driver()
    {

        $app = $this->createMock(Application::class);
        $cacheRepository = $this->createMock(CacheRepository::class);
        $app->method('make')
            ->with(CacheRepository::class)
            ->willReturn($cacheRepository);


        $this->assertInstanceOf(
            Converter::class,
            ConverterFactory::make(
                'eurobankDriver',
                $app
            )
        );
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
        $app = $this->createMock(Application::class);
        $this->expectException(DriverNotFound::class);
        ConverterFactory::make('not-existing', $app);
    }
}
