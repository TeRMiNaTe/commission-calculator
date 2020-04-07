<?php

namespace App\Models\Commissions;

use App\Models\Commissions\Commission;
use App\Models\Transaction;
use App\Models\User;
use DateTime;

class CashOutNaturalGratis extends Commission
{
    /**
     * Commission amount in percent
     * @var float
     */
    protected $commission_percent = 0.3;

    /**
     * Maximum commission gratis balance allowed per week
     * @var float
     */
    protected $gratis_balance = 1000;

    /**
     * Maximum gratis withdrawals allowed per week
     * @var float
     */
    protected $gratis_withdrawals_allowed = 3;
    
    public function appliesToTransaction(Transaction $transaction): bool
    {
        if ($transaction->type == 'cash_out') {
            if (!isset($transaction->user->last_cash_out)) {
                return true;
            } elseif (!$this->isSameWeek($transaction->date, $transaction->user->last_cash_out)) {
                return true;
            } elseif ($transaction->user->cash_out_count < $this->gratis_withdrawals_allowed) {
                return true;
            }
        }
        
        return false;
    }

    public function appliesToPerson(User $user): bool
    {
        return $user->type == 'natural';
    }

    public function apply(Transaction $transaction): float
    {
        if (!isset($transaction->user->last_cash_out) || !$this->isSameWeek($transaction->date, $transaction->user->last_cash_out)) {
            $this->resetGratisPeriod($transaction->user);
        }

        $amount_to_commission = max(0, $transaction->base_currency_amount - $transaction->user->cash_out_gratis_balance);

        $this->regisiterGratisTransaction($transaction);

        return $this->getCommissionAmount($amount_to_commission, $this->commission_percent);
    }

    protected function resetGratisPeriod(User $user): void
    {
        $user->cash_out_count = 0;
        $user->cash_out_gratis_balance = $this->gratis_balance;
    }

    protected function regisiterGratisTransaction(Transaction $transaction): void
    {
        $transaction->user->cash_out_count += 1;
        $transaction->user->cash_out_gratis_balance = max(0, $transaction->user->cash_out_gratis_balance - $transaction->base_currency_amount);
        $transaction->user->last_cash_out = DateTime::createFromFormat('Y-m-d', $transaction->date)->format('Y-m-d');
    }

    protected function isSameWeek($date1, $date2): bool
    {
        $date1_week = DateTime::createFromFormat('Y-m-d', $date1)->format('o-W');
        $date2_week = DateTime::createFromFormat('Y-m-d', $date2)->format('o-W');

        return $date1_week === $date2_week;
    }
}
