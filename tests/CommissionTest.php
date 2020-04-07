<?php

require __DIR__ . '/../index.php';

use App\Models\Transaction;
use App\Models\User;
use PHPUnit\Framework\TestCase;

final class CommissionTest extends TestCase
{
    public function __construct()
    {
        parent::__construct();

        global $app;
        $this->app = $app;
    }

    /**
     * Test the calculation of: cash in commission
     *
     * @return void
     */
    public function testCashInCommission(): void
    {
        $user = new User(1, 'natural');
        $transaction =  new Transaction();

        $transaction->date = '2019-02-23';
        $transaction->type = 'cash_in';
        $transaction->amount = 1000;
        $transaction->currency = 'EUR';
        $transaction->user = $user;

        $this->assertSame($this->app['managers']['commission']->calculateCommission($transaction), '0.30');
    }

    /**
     * Test the calculation of: maximum cash in commission
     *
     * @return void
     */
    public function testCashInCommissionMax(): void
    {
        $user = new User(2, 'legal');
        $transaction =  new Transaction();

        $transaction->date = '2019-02-24';
        $transaction->type = 'cash_in';
        $transaction->amount = 1000000;
        $transaction->currency = 'EUR';
        $transaction->user = $user;

        $this->assertSame($this->app['managers']['commission']->calculateCommission($transaction), '5.00');
    }

    /**
     * Test the calculation of: legal cash out commission
     *
     * @return void
     */
    public function testCashOutLegalCommission(): void
    {
        $user = new User(2, 'legal');
        $transaction =  new Transaction();

        $transaction->date = '2019-02-25';
        $transaction->type = 'cash_out';
        $transaction->amount = 2000;
        $transaction->currency = 'EUR';
        $transaction->user = $user;

        $this->assertSame($this->app['managers']['commission']->calculateCommission($transaction), '6.00');
    }

    /**
     * Test the calculation of: minimum legal cash out commission
     *
     * @return void
     */
    public function testCashOutLegalCommissionMin(): void
    {
        $user = new User(2, 'legal');
        $transaction =  new Transaction();

        $transaction->date = '2019-02-25';
        $transaction->type = 'cash_out';
        $transaction->amount = 100;
        $transaction->currency = 'EUR';
        $transaction->user = $user;

        $this->assertSame($this->app['managers']['commission']->calculateCommission($transaction), '0.50');
    }

    /**
     * Test the calculation of: natural cash out commissions
     *
     * @return void
     */
    public function testCashOutNaturalCommission(): void
    {
        $user = new User(1, 'natural');

        $transactions = [
            '2020-03-31' => 300,
            '2020-04-01' => 300,
            '2020-04-04' => 600,
            '2020-04-05' => 600,
        ];

        $expected_commissions = [
            '2020-03-31' => '0.00',
            '2020-04-01' => '0.00',
            '2020-04-04' => '0.60',
            '2020-04-05' => '1.80',
        ];

        foreach ($transactions as $date => $amount) {
            $transaction =  new Transaction();

            $transaction->date = $date;
            $transaction->type = 'cash_out';
            $transaction->amount = $amount;
            $transaction->currency = 'EUR';
            $transaction->user = $user;

            $this->assertSame($this->app['managers']['commission']->calculateCommission($transaction), $expected_commissions[$date]);
        }
    }
}
