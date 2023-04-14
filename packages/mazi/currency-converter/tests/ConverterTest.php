<?php

use Mazi\CurrencyConverter\Contracts\ConverterDriver;
use Mazi\CurrencyConverter\Converter;
use Mazi\CurrencyConverter\Symbol;
use PHPUnit\Framework\TestCase;


class ConverterTest extends TestCase
{
     /** @test */
    public function converter_can_change_amount_in_currency()
    {
        $eurobankDriver = $this->getMockForAbstractClass(
            ConverterDriver::class
        );

        $eurobankDriver->expects($this->once())->method('process');
        $converter = new Converter($eurobankDriver);
        $converter->change('200', Symbol::CAD);
    }
}
