<?php

use \Core\Router\Router;

Router::on(404, function ($req, $res) {
    $res->send("<h1>404</h1>");
});

Router::get("/", function($req, $res) {
    $res->redirect('/dashboard');
});

Router::get('/dashboard', function ($req, $res) {
    $res->send("<h1>Dashboard</h1>");
})->addMiddleware("AccountPolicies.isLoggedIn");

Router::get("/login", function ($req, $res) {
    $res->send("<h1>login</h1>");
});

Router::get("/home", function ($req, $res) {
    $res->send(" <h1>Home</h1> ");
});

