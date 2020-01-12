<?php

    if (!isset($argv[1])) {
        exit;
    }

    $models_dir = __DIR__ . '\..\models\\';
    require_once $models_dir . 'DBClass.php';
    require_once $models_dir . 'DBEntity.php';
    require_once $models_dir . 'Transaction.php';

    $confirmation_code = $argv[1];
    $t = new Transaction();
    $t->confirm($confirmation_code);