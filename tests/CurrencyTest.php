<?php

require __DIR__ . '/../index.php';

use App\Exceptions\CurrencyNotSupportedException;
use App\Models\Currencies\Euro;
use PHPUnit\Framework\TestCase;

final class CurrencyTest extends TestCase
{
    public function __construct()
    {
        parent::__construct();

        global $app;
        $this->app = $app;
    }

    /**
     * Test EUR currency code
     *
     * @return void
     */
    public function testEurCurrencyCode(): void
    {
        $this->assertClassHasAttribute('code', Euro::class);
    }

    /**
     * Test EUR currency rate
     *
     * @return void
     */
    public function testEurCurrencyRate(): void
    {
        $this->assertClassHasAttribute('rate', Euro::class);
    }

    /**
     * Test EUR currency precision
     *
     * @return void
     */
    public function testEurCurrencyPrecision(): void
    {
        $this->assertClassHasAttribute('precision', Euro::class);
    }

    /**
     * Test non-supported currencies
     *
     * @return void
     */
    public function testNotSupportedCurrency(): void
    {
        $this->expectException(CurrencyNotSupportedException::class);
        
        $this->app['managers']['currency']->round(0.07203, 'EURO');
    }

    /**
     * Test rounding & return value type
     *
     * @return void
     */
    public function testRounding(): void
    {
        $this->assertSame($this->app['managers']['currency']->round(0.07203, 'EUR'), 0.08);
    }

    /**
     * Test currency formatting
     *
     * @return void
     */
    public function testFormatting(): void
    {
        $this->assertSame($this->app['managers']['currency']->format(0.1, 'EUR'), '0.10');
    }

    /**
     * Test currency conversion
     * NOTE: this test is only valid for fixed rate currency conversion
     *
     * @return void
     */
    public function testConversion(): void
    {
        $this->assertSame(round($this->app['managers']['currency']->convert(10, 'USD', 'EUR'), 2), 8.7);
    }
}
