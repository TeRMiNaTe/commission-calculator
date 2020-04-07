<?php

namespace App\Models\Commissions;

use App\Models\Commissions\Commission;
use App\Models\Transaction;
use App\Models\User;

class CashOutNatural extends Commission
{
    /**
     * Commission amount in percent
     * @var float
     */
    protected $commission_percent = 0.3;
    
    public function appliesToTransaction(Transaction $transaction): bool
    {
        return $transaction->type == 'cash_out';
    }

    public function appliesToPerson(User $user): bool
    {
        return $user->type == 'natural';
    }

    public function apply(Transaction $transaction): float
    {
        return $this->getCommissionAmount($transaction->base_currency_amount, $this->commission_percent);
    }
}
