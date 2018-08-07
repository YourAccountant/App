<?php

session_start();

require __DIR__ . '/vendor/autoload.php';

use \Core\Foundation\Application;

(new Application(__DIR__, true))->run();
