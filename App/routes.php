<?php

use \Core\Router\Router;
use \Core\Router\Request;
use \Core\Router\Response;

// static pages
Router::setPrefix("/");
Router::setMiddleware([]);

// api
Router::setPrefix("/api/:version/");

Router::get("/", function ($req, $res) {
    $res->send("Welcome to the API");
});

// Router::setPrefix("/api/:version/oauth");
// Router::get("/:partner/grant", "OAuthController.grant");
    // ->setMiddleware(["AuthPolicy.hasToBeLoggedIn"]);

Router::post("authorize", "OAuthController.authorize")
    ->setMiddleware(['ApiResponse.validJson']);

Router::post("refresh", "OAuthController.refresh")
    ->setMiddleware(['ApiResponse.validJson']);

// Auth
Router::setPrefix("/api/:version/auth");
Router::setMiddleware(["ApiResponse.validJson"]);

Router::post("/loggedin", "AuthController.checkLoggedIn");
Router::post("/email-exists", "AuthController.emailExists");

Router::setMiddleware(["ApiResponse.validJson", /*"ApiResponse.isAjax"*/]);
Router::post("/signin", "AuthController.signin");
Router::post("/signup", "AuthController.signup");

Router::setMiddleware([]);
Router::get("/signout", "AuthController.signout");

Router::setPrefix("/api/:version/client");
Router::get(":clientId", "ClientController.getClient")
    ->setMiddleware('OAuthPolicy.authorize');

// app view
Router::on(404, function (Request $req, Response $res) {
    if ($req->pathIncludes("/api/")) {
        $res->send([
            "error" => "404"
        ], 404);
    } else {
        $res->view("app.php");
    }
});
