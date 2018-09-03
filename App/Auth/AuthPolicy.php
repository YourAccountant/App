<?php

namespace App\Auth;

use \Core\Foundation\Policy;
use \Core\Router\Request;
use \Core\Router\Response;

class AuthPolicy extends Policy
{

    public function hasToBeLoggedIn(Request $req, Response $res)
    {
        if ($this->getService('AuthService.checkLoggedIn')) {
            return;
        }

        return $res->redirect(Request::getHost() . "/login?prev=" . urlencode(Request::getFullUrl()));
    }
}
