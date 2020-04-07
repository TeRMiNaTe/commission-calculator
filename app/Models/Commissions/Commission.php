<?php

namespace App\Models\Commissions;

use App\Models\Transaction;
use App\Models\User;

abstract class Commission
{
    /**
     * Check if the commission applies to a given transaction
     * @param  Transaction $transaction
     * @return bool
     */
    abstract public function appliesToTransaction(Transaction $transaction): bool;

    /**
     * Check if the commission applies to a given user
     * @param  App\Models\User $user
     * @return bool
     */
    abstract public function appliesToPerson(User $user): bool;

    /**
     * Apply commission logic and return final commission amount
     * @param  App\Models\Transaction $transaction
     * @return float
     */
    abstract public function apply(Transaction $transaction): float;

    /**
     * Calculate percentage based commission amount
     * @param  float $amount
     * @param  float $commission_percent
     * @param  mixed $min
     * @param  mixed $max
     * @return float
     */
    protected function getCommissionAmount(float $amount, float $commission_percent, $min = 0, $max = null): float
    {
        $commission = ($commission_percent / 100) * $amount;

        if (!is_null($min)) {
            $commission = max($min, $commission);
        }
        if (!is_null($max)) {
            $commission = min($max, $commission);
        }
        
        return $commission;
    }
}
