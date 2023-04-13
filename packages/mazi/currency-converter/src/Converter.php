<?php

namespace Mazi\CurrencyConverter;

use Mazi\CurrencyConverter\Contracts\Converter as ContractsConverter;

class Converter implements ContractsConverter
{
    protected $driver;

    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    public function change(string $amount, string $currency): string
    {
        // handle conversion
        return $this->driver
            ->process($currency, $amount);
    }
}
