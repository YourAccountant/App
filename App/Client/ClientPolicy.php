<?php

namespace App\Client;

use \Core\Foundation\Policy;
use \Core\Router\Request;
use \Core\Router\Response;

class ClientPolicy extends Policy
{
    public function allowMe(Request $req, Response $res)
    {
        if (isset($req->params->clientId)
            && $this->getService('AuthService.isLoggedIn')
            && $req->params->clientId == 'me'
        ) {
            $req->params->clientId = $this->getService('AuthService.getClientId');
        }
    }

    public function owned(Request $req, Response $res)
    {
        if ((isset($req->params->clientId) && $req->params->clientId != $this->getService('AuthService.getClientId'))
            || !$this->getService('AuthService.isLoggedIn')
        ) {
            return $res->json([
                'error' => 'Forbidden'
            ], 403);
        }
    }
}
