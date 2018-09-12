<?php

namespace App\Client;

use \Core\Foundation\Controller;
use \Core\Router\Request;
use \Core\Router\Response;

class ClientController extends Controller
{
    public function getClient(Request $req, Response $res)
    {
        $client = new Client();
        $exists = $client->getBy('id', '=', $req->params->clientId);

        if ($exists) {
            return $res->json($client);
        } else {
            return $res->json(['result' => false, 'error' => 'user does not exists'], 400);
        }
    }
}
