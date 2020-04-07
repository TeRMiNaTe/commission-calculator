<?php

namespace App\Models\Commissions;

use App\Models\Commissions\Commission;
use App\Models\Transaction;
use App\Models\User;

class CashIn extends Commission
{
    /**
     * Commission amount in percent
     * @var float
     */
    protected $commission_percent = 0.03;

    /**
     * Maximum commission amount
     * @var float
     */
    protected $commission_max = 5;

    public function appliesToTransaction(Transaction $transaction): bool
    {
        return $transaction->type == 'cash_in';
    }

    public function appliesToPerson(User $user): bool
    {
        return true;
    }

    public function apply(Transaction $transaction): float
    {
        return $this->getCommissionAmount($transaction->base_currency_amount, $this->commission_percent, 0, $this->commission_max);
    }
}
