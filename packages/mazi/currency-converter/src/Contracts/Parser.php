<?php

namespace Mazi\CurrencyConverter\Contracts;

interface Parser
{
    /**
     * Parse the data to array
     *
     * @return  array   Parsed data
     */
    public function parse(string $xmlData): array;
}
