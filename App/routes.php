<?php

use \Core\Router\Router;


Router::get('/dashboard', function ($req, $res) {
    $res->send("<h1>Dashboard</h1>");

})->addMiddleware("AccountPolicies.isLoggedIn");

Router::get("/login", function ($req, $res) {
    $res->send("<h1>login</h1>");
});

Router::on(404, function ($req, $res) {
    $res->send("<h1>404</h1>");
});
