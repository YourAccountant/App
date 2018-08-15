<?php

namespace App\Auth;

use \Core\Foundation\Controller;
use \Core\Router\Request;
use \Core\Router\Response;

class AuthController extends Controller
{
    public function checkLoggedIn(Request $req, Response $res)
    {
        $isLoggedIn = $this->getService('AuthService.isLoggedIn');
        $res->send(['result' => $isLoggedIn ? 1 : 0], $isLoggedIn ? 200 : 401);
    }
}
