<?php

namespace App\Managers;

use App\Exceptions\FileNotFoundException;
use App\Managers\BaseManager;
use App\Models\Transaction;
use App\Models\User;

class TransactionManager extends BaseManager
{
    /**
     * Iterates over a list of transactions and outputs their commissions to STDOUT
     * @param  string $filename
     * @return void
     */
    public function processCommissionFile($filename): void
    {
        $this->app['managers']['file']->checkFile($filename);

        // Store users in memory
        $users = [];

        $file = fopen($filename, 'r');
        while(!feof($file)) {
            $transaction_data = fgetcsv($file);

            // Map properties from CSV
            $transaction_date       = $transaction_data[0];
            $user_id                = $transaction_data[1];
            $user_type              = $transaction_data[2];
            $transaction_type       = $transaction_data[3];
            $transaction_amount     = $transaction_data[4];
            $transaction_currency   = $transaction_data[5];

            // Fetch users from memory
            if (array_key_exists($user_id, $users)) {
                $user = $users[$user_id];
            } else {
                $user = new User($user_id, $user_type);
                $users[$user_id] = $user;
            }

            $transaction = new Transaction();
            $transaction->date = $transaction_date;
            $transaction->user = $user;
            $transaction->type = $transaction_type;
            $transaction->amount = (float) $transaction_amount;
            $transaction->currency = $transaction_currency;

            $commission = $this->app['managers']['commission']->calculateCommission($transaction);

            fwrite(STDOUT, $commission.PHP_EOL);
        }

        fclose($file);
    }
}
