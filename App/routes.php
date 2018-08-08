<?php

use \Core\Router\Router;
use \Core\Database\Migration\Migration;
use \Core\Database\QueryBuilder;
use \Core\Database\Generator;


Router::get('home', "HomeController.show");
