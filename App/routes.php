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

Router::get("/administration/:administrationId", "AdministrationController.getOne")
    ->setMiddleware('AdministrationPolicy.isOwned');

Router::post("/administration", "AdministrationController.create")
    ->setMiddleware(['ApiResponse.validJson']);

Router::put("/administration/:administrationId", "AdministrationController.update")
    ->setMiddleware(['ApiResponse.validJson', 'AdministrationPolicy.isOwned']);

Router::delete("/administration/:administrationId", "AdministrationController.delete")
    ->setMiddleware('AdministrationPolicy.isOwned');

// Accounts
Router::get("/administration/:administrationId/account", "AccountController.get")
    ->setMiddleware('AdministrationPolicy.isOwned');

Router::get("/administration/:administrationId/account/:accountId", "AccountController.getOne")
    ->setMiddleware('AdministrationPolicy.isOwned');

Router::post("/administration/:administrationId/account", "AccountController.create")
    ->setMiddleware(['ApiResponse.validJson', 'AdministrationPolicy.isOwned']);

Router::put("/administration/:administrationId/account/:accountId", "AccountController.update")
    ->setMiddleware(['ApiResponse.validJson', 'AdministrationPolicy.isOwned']);

Router::delete("/administration/:administrationId/account/:accountId", "AccountController.delete")
    ->setMiddleware('AdministrationPolicy.isOwned');

// Journals
Router::get("/administration/:administrationId/journal", "JournalController.get")
    ->setMiddleware('AdministrationPolicy.isOwned');

Router::get("/administration/:administrationId/journal/:journalId", "JournalController.getOne")
    ->setMiddleware('AdministrationPolicy.isOwned');

Router::post("/administration/:administrationId/journal", "JournalController.create")
    ->setMiddleware(['ApiResponse.validJson', 'AdministrationPolicy.isOwned']);

Router::put("/administration/:administrationId/journal/:journalId", "JournalController.update")
    ->setMiddleware(['ApiResponse.validJson', 'AdministrationPolicy.isOwned']);

Router::delete("/administration/:administrationId/journal/:journalId", "JournalController.delete")
    ->setMiddleware('AdministrationPolicy.isOwned');
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
