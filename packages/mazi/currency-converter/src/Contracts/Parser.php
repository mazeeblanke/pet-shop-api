<?php

namespace Mazi\CurrencyConverter\Contracts;

interface Parser
{

    /**
     * Parse the data to array
     *
     * @param   string  $xmlData
     *
     * @return  array   Parsed data
     */
    public function parse(string $xmlData): array;

}
