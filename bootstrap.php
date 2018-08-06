<?php

session_start();

require __DIR__ . '/vendor/autoload.php';

use \Core\Foundation\Application;

$app = (new Application(__DIR__))
    ->setConfig('.config')
    ->setCache('.cache')
    ->setApp('App')
    ->setViews('views')
    ->setConnection()
    ->setDebug('log')
    ->run();
