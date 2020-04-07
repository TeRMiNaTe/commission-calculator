<?php

namespace App\Managers;

use App\Exceptions\CurrencyNotSupportedException;
use App\Exceptions\CurrencyNotFoundException;
use App\Managers\BaseManager;

class CurrencyManager extends BaseManager
{
    /**
     * List of active currencies and their class names
     * @var array
     */
    protected $currency_map = [
        'EUR' => 'Euro',
        'USD' => 'UnitedStatesDollar',
        'JPY' => 'JapaneseYen',
    ];

    /**
     * Currency namespace
     * @var string
     */
    protected $currency_ns = 'App\\Models\\Currencies\\';

    /**
     * Holding array for currencies
     * @var array
     */
    protected $currencies = [];

    /**
     * Used to round up based on the decimal place, for example rounding up to the nearest penny
     * https://stackoverflow.com/a/48933199
     * @param float $amount
     * @param string $currency_code
     * @return float
     */
    public function round(float $amount, $currency_code): float
    {
        $currency = $this->loadCurrency($currency_code);

        $precision = $currency->precision;
        $offset = 0.5;
        if ($precision !== 0) {
            $offset /= 10 ** $precision;
        }
        $final = round($amount + $offset, $precision, PHP_ROUND_HALF_DOWN);
        return ($final == -0 ? 0 : $final);
    }

    /**
     * Format the amount using currency precision
     * @param float $amount
     * @param string $currency_code
     * @return string
     */
    public function format(float $amount, $currency_code): string
    {
        $currency = $this->loadCurrency($currency_code);
        $amount = $this->round($amount, $currency_code);

        return number_format($amount, $currency->precision, '.', '');
    }

    /**
     * Convert amount between currencies
     * @param  float $amount
     * @param  string $from_code
     * @param  string $to_code
     * @return float
     */
    public function convert(float $amount, $from_code, $to_code): float
    {
        $from = $this->loadCurrency($from_code);
        $to = $this->loadCurrency($to_code);

        // Convert
        if ($from_code == $to_code) {
            return $amount;
        } else {
            // We can only calculate the conversion if one of the two currencies is the main currency (rate === 1)
            if ($from->rate !== 1 && $to->rate !== 1) {
                return 0;
            } elseif ($from->rate == 1) {
                return $amount * $to->rate;
            } elseif ($to->rate == 1) {
                return $amount * (1 / $from->rate);
            }
        }

        return 0;
    }

    /**
     * Checks if a currency can be loaded and loads it into the holding array making it available for use
     * @param  string $code
     * @return mixed
     */
    protected function loadCurrency($code)
    {
        if (array_key_exists($code, $this->currencies)) {
            return $this->currencies[$code];
        }

        if (!array_key_exists($code, $this->currency_map)) {
            throw new CurrencyNotSupportedException('"'.$code.'" is not a supported currency. List of valid currencies: '.implode(', ', array_keys($this->currency_map)));
        }

        $class = $this->currency_ns.$this->currency_map[$code];

        if (!class_exists($class)) {
            throw new CurrencyNotFoundException('"'.$class.'" could not be found');
        }
        
        $this->currencies[$code] = new $class();
        return $this->currencies[$code];
    }
}
