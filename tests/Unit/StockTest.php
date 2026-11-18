<?php

namespace Tests\Unit;

use App\Support\Stock;
use Tests\TestCase;

class StockTest extends TestCase
{
    public function test_status_bands_follow_the_low_stock_level(): void
    {
        config(['portal.low_stock' => 25]);

        $this->assertSame('Out of stock', Stock::status(0)[0]);
        $this->assertSame('Out of stock', Stock::status(-3)[0]);
        $this->assertSame('Low', Stock::status(25)[0]);
        $this->assertSame('In stock', Stock::status(26)[0]);
    }
}
