<?php

namespace App\Administration;

use \Core\Foundation\Policy;
use \Core\Router\Request;
use \Core\Router\Response;

class AdministrationPolicy extends Policy
{
    public function isOwned(Request $req, Response $res)
    {
        if (!$this->isset($req->params, 'administrationId')) {
            return $res->json([
                'error' => 'missing id'
            ], 400);
        }

        $admin = new Administration();
        if (!$admin->isOwned($req->params->administrationId)) {
            return $res->json([
                'error' => 'forbidden'
            ], 401);
        }
    }
}
