<?php

namespace App\Auth\OAuth;

use \Core\Foundation\Policy;
use \Core\Router\Request;
use \Core\Router\Response;

class OAuthPolicy extends Policy
{
    public function checkBearer(Request $req, Response $res)
    {
        $bearer = $req->header->authorization;

        if ($bearer == null) {
            return $res->send(['error' => 'not authorized'], 402);
        }

        $bearer = str_replace('Bearer ', '', $bearer);

        $token = new OAuthToken();
        $token->getByBearer($bearer);

        if ($token->get('date_expiration') < date('Y-m-d H:i:s')) {
            return $res->send(['error' => 'token expired'], 400);
        }

        $req->set('token', $token);
    }
}
