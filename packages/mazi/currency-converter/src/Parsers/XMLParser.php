<?php

namespace Mazi\CurrencyConverter\Parsers;

use Mazi\CurrencyConverter\Contracts\Parser;
use Mazi\CurrencyConverter\Exceptions\EmptyXML;

class XMLParser implements Parser
{
    /**
     * Do the hard work of parsing
     *
     * @param   string  $xmlData
     *
     * @return  array
     */
    public function parse(string $xmlData): array
    {
        $xml = simplexml_load_string(
            $xmlData,
            'SimpleXMLElement',
            LIBXML_NOCDATA
        );

        if (!$xml) {
            throw new EmptyXML("XML data is empty, unable to parse!");
        }

        return $this->getData($xml);
    }

    /**
     * Get data in array format
     *
     * @param   [type]  $xml
     *
     * @return  array
     */
    protected function getData($xml): array
    {
        return json_decode(
            json_encode($xml),
            true
        );
    }
}

