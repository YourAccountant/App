<?php

namespace App\Util;

use \Core\Foundation\Policy;
use \Core\Router\Request;
use \Core\Router\Response;

class ApiResponse extends Policy
{

    public function validJson(Request $req, Response $res)
    {
        $req->json();

        if (json_last_error() == JSON_ERROR_NONE) {
            return;
        }

        return $res->json(['error' => 'invalid json'], 400);
    }

    public function isAjax(Request $req, Response $res)
    {
        if (Request::isAjax()) {
            return;
        }

        return $res->json(['error' => 'unknown server'], 401);
    }
}
