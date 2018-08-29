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
        $res->send(['result' => $isLoggedIn ? true : false], $isLoggedIn ? 200 : 401);
    }

    public function login(Request $req, Response $res)
    {

    }

    public function signup(Request $req, Response $res)
    {

    }

    public function emailExists(Request $req, Response $res)
    {
        $exists = $this->getService('AuthService.checkEmailExists', $req->json()->email);
        $res->send(['result' => $exists ? true : false], $exists ? 200 : 403);
    }
}
