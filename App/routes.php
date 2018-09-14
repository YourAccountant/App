<?php

use \Core\Router\Router;
use \Core\Router\Request;
use \Core\Router\Response;

/**
OAuth backlog

Router::setPrefix("/api/:version/oauth");
Router::post("/partner", "OAuthController.createPartner")
    ->setMiddleware(["OAuthPolicy.authorize", "ApiResponse.validJson"]);

Router::get("/:partner/grant", "OAuthController.grant")
    ->setMiddleware(["OAuthPolicy.authorize"]);

Router::post("authorize", "OAuthController.authorize")
    ->setMiddleware(['ApiResponse.validJson']);

Router::post("refresh", "OAuthController.refresh")
    ->setMiddleware(['ApiResponse.validJson']);
 */

/**
 * Session Auth
 */
Router::setPrefix("/api/:version/auth");
Router::setMiddleware(["ApiResponse.validJson"]);
Router::post("/email-exists", "AuthController.emailExists");
Router::post("/signin", "AuthController.signin");
Router::post("/signup", "AuthController.signup");
Router::get("/signout", "AuthController.signout")->noMiddleware();

/**
 * Protected resources
 */
Router::setMiddleware(['OAuthPolicy.authorize']);
Router::setPrefix("/api/:version/");

// Clients

Router::get("/client/:clientId", "ClientController.getClient")
    ->setMiddleware(['ClientPolicy.allowMe', 'ClientPolicy.owned']);

// Administrations

Router::get("/administration", "AdministrationController.get");
Router::get("/administration/:administrationId", "AdministrationController.getOne");

Router::post("/administration", "AdministrationController.create")
    ->setMiddleware(['ApiResponse.validJson']);

Router::put("/administration/:administrationId", "AdministrationController.update")
    ->setMiddleware(['ApiResponse.validJson']);

Router::delete("/administration/:administrationId", "AdministrationController.delete");

/**
 * Fallback
 */
Router::on(404, function (Request $req, Response $res) {
    if ($req->pathIncludes("/api/")) {
        return $res->json([
            "error" => "404"
        ], 404);
    }

    return $res->view("app.php");
});
