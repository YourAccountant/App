<?php

namespace App\Guard;

use \Core\Foundation\Policy;
use \Core\Router\Request;
use \Core\Router\Response;

class AccountPolicies extends Policy
{

    public function isLoggedIn(Request $req, Response $res)
    {
        if (!$_SESSION['user']['isLoggedIn']) {
            $_SESSION['user']['isLoggedIn'] = false;
            $res->redirect('/login');
        }
    }

    public function isOwnedBy(Request $req, Response $res)
    {
        if ($count < 1) {
            if ($req->isAjax()) {
                $res->redirect('/dashboard');
            } else {
                $res->send(['errors' => [['code' => 403,'msg' => 'Not allowed to get this resource']]], 403);
            }
        }
    }

    public function hasAuthority()
    {
        $isAdmin = true;
        if (!$isAdmin) {
            if ($req->isAjax()) {
                $res->redirect('/dashboard');
            } else {
                $res->send(['errors' => [['code' => 403,'msg' => 'Not allowed to get this resource']]], 403);
            }
        }
    }
}
