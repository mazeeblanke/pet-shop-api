<?php

namespace Mazi\CurrencyConverter\Contracts;

interface ConverterDriver
{
    /**
     * Process the work
     *
     * @param   string  $targetCurrency  currency to be converted to
     * @param   string  $amount          amount to convert
     *
     * @return  string                   conversion amount
     */
    public function process(string $targetCurrency, string $amount): float;
}
