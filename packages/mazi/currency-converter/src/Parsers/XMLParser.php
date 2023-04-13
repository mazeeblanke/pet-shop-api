<?php

namespace Mazi\CurrencyConverter\Parsers;

use Mazi\CurrencyConverter\Contracts\Parser;

class XMLParser implements Parser
{
    public function parse($xmlData): array
    {
        $xml = simplexml_load_string(
            $xmlData,
            'SimpleXMLElement',
            LIBXML_NOCDATA
        );

        if (!$xml) {
            // throw error
        }

        return $this->getData($xml);
    }

    protected function getData($xml): array
    {
        return json_decode(
            json_encode($xml),
            true
        );
    }
}

