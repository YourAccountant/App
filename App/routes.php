<?php

use \Core\Router\Router;
use \Core\Router\Request;
use \Core\Router\Response;

Router::on(404, function (Request $req, Response $res) {
    $res->view("app.php");
});

Router::setPrefix("/api/:version/");
Router::get('user', function ($req, $res) {
    echo "user";
});
