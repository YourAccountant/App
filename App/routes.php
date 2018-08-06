<?php

use \Core\Router\Router;

Router::get("home", "HomeController.show")
    ->addMiddleware('HomePolicies.isAllowed');
