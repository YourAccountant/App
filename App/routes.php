<?php

use \Core\Router\Router;
use \Core\Database\Migration\Migration;
use \Core\Database\QueryBuilder;
use \Core\Database\Generator;


Router::get("/migration", "HomeController.test2");

Router::get('/query', 'HomeController.test');
