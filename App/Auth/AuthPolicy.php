<?php

namespace App\Auth;

use \Core\Foundation\Policy;
use \Core\Router\Request;
use \Core\Router\Response;

class AuthPolicy extends Policy
{
    public function confirmCrsf(Request $req, Response $res)
    {
        if (!isset($_POST['csrf']) && !isset($req->cookie->csrf)) {
            return;
        }

        if ($_POST['csrf'] != $req->cookie->csrf) {
            return $res->json([
                'error' => 'failed to authorize csrf'
            ], 401);
        }

    }
}
