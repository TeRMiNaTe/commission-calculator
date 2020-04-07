<?php

namespace App\Managers;

use App\Exceptions\CommissionNotFoundException;
use App\Managers\BaseManager;
use App\Models\Transaction;
use App\Models\Commissions\Commission;

class CommissionManager extends BaseManager
{
    /**
     * List of active commission ordered by priority
     * @var array
     */
    protected $commission_list = ['CashIn', 'CashOutLegal', 'CashOutNaturalGratis', 'CashOutNatural'];

    /**
     * Commission namespace
     * @var string
     */
    protected $commission_ns = 'App\\Models\\Commissions\\';

    /**
     * The currency in which commission amounts will be calculated
     * @var string
     */
    protected $base_currency = 'EUR';

    /**
     * Iterates over the current active commissions and applies the first applicable one
     * @param  App\Models\Transaction $transaction
     * @return string
     */
    public function calculateCommission(Transaction $transaction): string
    {
        foreach ($this->commission_list as $commission_name) {
            $class = $this->commission_ns.$commission_name;

            if (!class_exists($class)) {
                throw new CommissionNotFoundException('"'.$class.'" could not be found');
            }

            $commission = new $class();

            if ($this->applicable($transaction, $commission)) {
                $commission_amount = $this->calculate($transaction, $commission);

                return $this->app['managers']['currency']->format($commission_amount, $transaction->currency);
            }
        }
    }

    /**
     * Checks if a commission is applicable to a given transaction
     * @param  App\Models\Transaction $transaction
     * @param  App\Models\Commissions\Commission $commission
     * @return bool
     */
    protected function applicable(Transaction $transaction, Commission $commission): bool
    {
        return $commission->appliesToTransaction($transaction) && $commission->appliesToPerson($transaction->user);
    }

    /**
     * Calculates and applies a commission to a transaction and returns the amount
     * @param  App\Models\Transaction $transaction
     * @param  App\Models\Commissions\Commission $commission
     * @return number
     */
    protected function calculate(Transaction $transaction, Commission $commission): float
    {
        // Commissions are calculated in the specified base currency
        $transaction->base_currency_amount = $this->app['managers']['currency']->convert($transaction->amount, $transaction->currency, $this->base_currency);

        $base_currency_commission = $commission->apply($transaction);

        // Revert to original currency
        return $this->app['managers']['currency']->convert($base_currency_commission, $this->base_currency, $transaction->currency);
    }
}
