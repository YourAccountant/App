<?php

use \Core\Router\Router;
use \Core\Database\Migration\Migration;
use \Core\Database\QueryBuilder;
use \Core\Database\Generator;


Router::get('home', "HomeController.show");

Router::on(404, function ($req, $res) {
    echo "<h1>404</h1>";
});
