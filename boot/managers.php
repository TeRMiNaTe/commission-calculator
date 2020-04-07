<?php

use App\Managers\CommissionManager;
use App\Managers\CurrencyManager;
use App\Managers\FileManager;
use App\Managers\TransactionManager;

$app['managers'] = [];

$app['managers']['file'] = new FileManager($app);
$app['managers']['currency'] = new CurrencyManager($app);
$app['managers']['commission'] = new CommissionManager($app);
$app['managers']['transaction'] = new TransactionManager($app);
