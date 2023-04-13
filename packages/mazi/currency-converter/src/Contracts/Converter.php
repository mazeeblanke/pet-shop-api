<?php

namespace Mazi\CurrencyConverter\Contracts;

interface Converter
{
    /**
     * Perform currency conversion
     *
     * @param   string  $amount    amount to convert
     * @param   string  $currency  currency to convert to
     *
     * @return  string             converted value
     */
    public function change(string $amount, string $currency): string;
}
