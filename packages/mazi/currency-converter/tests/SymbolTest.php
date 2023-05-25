<?php

use Mazi\CurrencyConverter\Symbol;
use PHPUnit\Framework\TestCase;

/**
 * SymbolTest
 */
class SymbolTest extends TestCase
{
    /** @test */
    public function can_get_all_symbols()
    {
        $this->assertCount(167, Symbol::all());
    }

    /** @test */
    public function can_get_a_symbol_name()
    {
        $this->assertEquals('Albanian Lek', Symbol::name(Symbol::ALL));
        $this->assertEquals('Euro', Symbol::name(Symbol::EUR));
    }

    /** @test */
    public function can_get_a_list_of_all_symbols()
    {
        $this->assertCount(167, Symbol::names());
        $this->assertEquals('Albanian Lek', Symbol::names()[Symbol::ALL]);
        $this->assertEquals('Euro', Symbol::names()[Symbol::EUR]);
    }
}
