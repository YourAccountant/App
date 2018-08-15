<?php

use \Core\Router\Router;
use \Core\Router\Request;
use \Core\Router\Response;

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

// api
Router::setPrefix("/api/:version/");

Router::get("/user", function () {
    echo "user";
});
