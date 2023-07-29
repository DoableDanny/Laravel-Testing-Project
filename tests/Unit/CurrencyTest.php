<?php

namespace Tests\Unit;

use App\Services\CurrencyService;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_convert_usd_to_eur_successfully(): void
    {
        $res = (new CurrencyService())->convert(100, 'usd', 'eur');

        $this->assertEquals(98, $res); 
    }

    public function test_convert_usd_to_gbp_successfully(): void
    {
        $res = (new CurrencyService())->convert(100, 'usd', 'gbp');

        $this->assertEquals(0, $res); 
    }
}
